#!/usr/bin/env bash
# Checks that pages are closed without a login, logs in with the demo account
# and opens every page, failing on HTTP errors or PHP fatal errors. Then submits a new
# application (with mistakes, then right, then twice), changes a rule of the
# grid in the configuration, submits an update, runs a shopping card through
# its life and removes the applicant, checking what ends up in the database
# each time. Along the way it tries a few forged requests.
# PHP warnings are reported but tolerated unless STRICT=1.
set -euo pipefail

cd "$(dirname "$0")/.."
base="${BASE_URL:-http://localhost:4330}"
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
failed=0

user="${SMOKE_USER:-emporiosolidale}"
password="${SMOKE_PASSWORD:-IlSole}"

sql() { docker compose exec -T db mariadb -N -uroot -pdev EmporioSolidale -e "$1"; }

report() {
    printf '%-46s %s\n' "$1" "$2"
    [ "$2" = ok ] || failed=1
}

# Without a login every page sends back to the login page, and the export
# gives nothing away.
status=ok
for page in List.php Config.php Grad_Export.php ModuloDomanda.php; do
    code="$(curl -sS -o "$tmp/body" -w '%{http_code}' "$base/FileToBeExecuted/$page")"
    [ "$code" = 302 ] && [ ! -s "$tmp/body" ] || status="FAIL $page answered $code without a login"
done
report "pages without a login" "$status"

# form_token <page>: the token every form of the session carries. It changes
# when the session does: at login, and when a closed page is asked for.
form_token() {
    curl -sS -b "$tmp/jar" -c "$tmp/jar" "$base/$1" | grep -oE "name='csrf' value='[^']*'" | head -1 | sed -E "s/.*value='([^']*)'/\1/"
}
touch "$tmp/jar"
csrf="$(form_token index.php)"

status=ok
code="$(curl -sS -b "$tmp/jar" -c "$tmp/jar" -o "$tmp/body" -w '%{http_code}' --data-urlencode "csrf=$csrf" \
    --data-urlencode "username=$user" --data-urlencode 'psw=not-the-password' "$base/index.php")"
[ "$code" = 200 ] && grep -q 'Username o password sbagliati' "$tmp/body" || status="FAIL wrong password answered $code"
code="$(curl -sS -b "$tmp/jar" -o /dev/null -w '%{http_code}' "$base/FileToBeExecuted/List.php")"
[ "$code" = 302 ] || status="FAIL logged in with a wrong password"
report "login with a wrong password" "$status"

# After five wrong passwords even the right one is refused, for a while.
sql 'DELETE FROM TentativiAccesso'
status=ok
for _ in 1 2 3 4 5; do
    csrf="$(form_token index.php)"
    curl -sS -b "$tmp/jar" -c "$tmp/jar" -o /dev/null --data-urlencode "csrf=$csrf" \
        --data-urlencode "username=$user" --data-urlencode 'psw=not-the-password' "$base/index.php"
done
csrf="$(form_token index.php)"
code="$(curl -sS -b "$tmp/jar" -c "$tmp/jar" -o "$tmp/body" -w '%{http_code}' --data-urlencode "csrf=$csrf" \
    --data-urlencode "username=$user" --data-urlencode "psw=$password" "$base/index.php")"
[ "$code" = 200 ] && grep -q 'Troppi tentativi' "$tmp/body" || status="FAIL right password after five wrong ones answered $code"
sql 'DELETE FROM TentativiAccesso'
report "login after too many wrong passwords" "$status"

status=ok
code="$(curl -sS -b "$tmp/jar" -o /dev/null -w '%{http_code}' \
    --data-urlencode "username=$user" --data-urlencode "psw=$password" "$base/index.php")"
[ "$code" = 400 ] || status="FAIL login without the form token answered $code"
report "login without the form token" "$status"

csrf="$(form_token index.php)"
code="$(curl -sS -b "$tmp/jar" -c "$tmp/jar" -o /dev/null -w '%{http_code}' --data-urlencode "csrf=$csrf" \
    --data-urlencode "username=$user" --data-urlencode "psw=$password" "$base/index.php")"
if [ "$code" != 302 ]; then
    echo "FAIL login: answered $code" >&2
    exit 1
fi
csrf="$(form_token FileToBeExecuted/Domanda.php)"

for page in index.php home.html FileToBeExecuted/*.php; do
    # Needs the choices made in Domanda.php: covered by the flows below.
    [ "$page" = FileToBeExecuted/ModuloDomanda.php ] && continue
    code="$(curl -sS -b "$tmp/jar" -o "$tmp/body" -w '%{http_code}' "$base/$page")"
    warnings="$(grep -cE '<b>(Warning|Notice|Deprecated)</b>' "$tmp/body" || true)"
    status=ok
    [ "$code" = 200 ] || status="FAIL http $code"
    if grep -qE '<b>(Fatal error|Parse error)</b>' "$tmp/body"; then status="FAIL fatal error"; fi
    if [ "${STRICT:-0}" = 1 ] && [ "$warnings" != 0 ]; then status="FAIL $warnings warnings"; fi
    printf '%-46s %-24s warnings=%s\n' "$page" "$status" "$warnings"
    [ "$status" = ok ] || failed=1
done

tax_code=SMKTST80A01A944Z
# shellcheck disable=SC2329  # invoked by the trap
cleanup() {
    sql "DELETE FROM Richiedente WHERE Cod_Fiscale='$tax_code';
         DELETE FROM Tessera WHERE Codice='SMOKECARD001';
         UPDATE RegolePunteggio SET Punti=10 WHERE Chiave='minors_more_adults'" || true
    rm -rf "$tmp"
}
trap cleanup EXIT
sql "DELETE FROM Richiedente WHERE Cod_Fiscale='$tax_code'"

minor_year=$(($(date +%Y) - 5))

# The value of a field of the application: unemployed head of the family,
# working spouse, one minor with a severe disability, ISEE 2000, public
# housing, income support, rent arrears, other debts worth 4 points, other
# contributions worth 2.
application_value() {
    case "$1" in
        CognomeRichiedente) echo Smoke ;;
        NomeRichiedente) echo Test ;;
        LuogoNascitaRichiedente | Residenza | 'LuogoNascita['[123]']') echo Bologna ;;
        DataNascitaRichiedente | 'DataNascita[1]') echo 20/04/1980 ;;
        'DataNascita[2]') echo 25/09/1982 ;;
        'DataNascita[3]') echo "30/12/$minor_year" ;;
        Cittadinanza) echo ITA ;;
        Via) echo 'Via Esempio 2' ;;
        CodiceFiscale) echo "$tax_code" ;;
        TipoDiAlloggio) echo Popolare ;;
        DocumentoDIdent) echo AB1234567 ;;
        Telefono) echo 3330000999 ;;
        Mail) echo smoke.test@example.com ;;
        ServiziSociali) echo No ;;
        nComponentiNucleoFamiliare) echo 3 ;;
        IndiceISEE) echo 2000 ;;
        'Nome['[123]']') echo 'Smoke Test' ;;
        'Parentela[1]') echo Capofamiglia ;;
        'Parentela[2]') echo Coniuge ;;
        'Parentela[3]') echo Figlio ;;
        'Occupazione[1]') echo Disoccupato ;;
        'Occupazione[2]') echo Altro ;;
        'Anno[7]') echo 2025 ;;
        'ValoreENT[5]') echo 500 ;;
        InvaliditaGraveMinori) echo 1 ;;
        AltriDebiti) echo 4 ;;
        AltriContributi) echo 2 ;;
    esac
}

# open_form <kind>: chooses the kind of application in Domanda.php and
# downloads the form.
open_form() {
    curl -sS -b "$tmp/jar" -c "$tmp/jar" -o /dev/null \
        --data-urlencode "csrf=$csrf" --data-urlencode "TipoDiDomanda=$1" -d nComponentiNucleoFamiliare=3 \
        --data-urlencode "CodiceFiscale=$tax_code" "$base/FileToBeExecuted/Domanda.php"
    curl -sS -b "$tmp/jar" -o "$tmp/form" "$base/FileToBeExecuted/ModuloDomanda.php"
}

# submit_application <kind> [field=value ...]: opens the form for that kind of
# application and sends every field it has, with the values above unless
# overridden.
submit_application() {
    local kind="$1" name value override args=()
    shift
    open_form "$kind"
    while IFS= read -r name; do
        value="$(application_value "$name")"
        [ "$name" = TipoDiDomanda ] && value="$kind"
        [ "$name" = csrf ] && value="$csrf"
        for override in "$@"; do
            [ "${override%%=*}" = "$name" ] && value="${override#*=}"
        done
        args+=(--data-urlencode "$name=$value")
    done < <(grep -oE "name=['\"][^'\"]+['\"]" "$tmp/form" | sed -E "s/name=['\"]([^'\"]+)['\"]/\1/" | sort -u)
    curl -sS -b "$tmp/jar" -o "$tmp/body" "${args[@]}" "$base/FileToBeExecuted/ModuloDomanda.php"
}

# check <label> <expected scores> <expected assessment>
# Scores: ISEE, minors, unemployment, disability, debts, benefits, total.
# Assessment: adults with total and severe disability, minors, other debts, other contributions.
check() {
    local label="$1" status=ok scores members assessment
    scores="$(sql "SELECT CONCAT_WS(' ', PunteggioISEE, PunteggioPresenzaMinori, PunteggioDisoccupazione, PunteggioInvalidi, PunteggioSituazioneDebitoria, PunteggioBenefit, PunteggioTotale) FROM Punteggio WHERE CodFamiglia='$tax_code'")"
    members="$(sql "SELECT CONCAT_WS(' ', COUNT(*), MIN(DataNascita)) FROM Membri WHERE CodFamiglia='$tax_code'")"
    assessment="$(sql "SELECT CONCAT_WS(' ', InvaliditaTotaleAdulti, InvaliditaGraveAdulti, InvaliditaGraveMinori, AltriDebiti, AltriContributi) FROM ValutazioneGriglia WHERE CodFamiglia='$tax_code'")"
    [ "$members" = "3 1980-04-20" ] || status="FAIL members stored '$members', expected '3 1980-04-20'"
    [ "$assessment" = "$3" ] || status="FAIL assessment stored '$assessment', expected '$3'"
    [ "$scores" = "$2" ] || status="FAIL scores '$scores', expected '$2'"
    if grep -qE '<b>(Fatal error|Parse error)</b>' "$tmp/body"; then
        status="FAIL $(grep -oE '<b>(Fatal error|Parse error)</b>:[^<]*' "$tmp/body" | head -1 | sed -E 's/<[^>]+>//g')"
    fi
    printf '%-46s %s\n' "$label" "$status"
    [ "$status" = ok ] || failed=1
}

# A form with mistakes is not saved and comes back filled in, with the reasons.
submit_application 'Domanda Nuova' 'DataNascitaRichiedente=31/02/1980' 'Mail=nobody'
status=ok
[ "$(sql "SELECT COUNT(*) FROM Richiedente WHERE Cod_Fiscale='$tax_code'")" = 0 ] || status="FAIL saved despite the mistakes"
grep -q 'Data di nascita del richiedente' "$tmp/body" || status="FAIL birth date mistake not reported"
grep -q 'E-mail: indirizzo non valido' "$tmp/body" || status="FAIL e-mail mistake not reported"
grep -q 'name="CognomeRichiedente" maxlength=.30. value="Smoke"' "$tmp/body" || status="FAIL typed values lost"
if grep -qE '<b>(Fatal error|Parse error|Warning)</b>' "$tmp/body"; then status="FAIL PHP error"; fi
printf '%-46s %s\n' "application with mistakes" "$status"
[ "$status" = ok ] || failed=1

submit_application 'Domanda Nuova'
check 'new application' '80 10 15 10 9 -22 102' '0 0 1 4 2'

# The same applicant cannot be entered twice as new.
submit_application 'Domanda Nuova' IndiceISEE=7000
status=ok
grep -q 'gi&agrave; registrato\|già registrato' "$tmp/body" || status="FAIL duplicate not reported"
[ "$(sql "SELECT ISEE FROM Richiedente WHERE Cod_Fiscale='$tax_code'")" = 2000 ] || status="FAIL duplicate overwrote the applicant"
printf '%-46s %s\n' "duplicate application" "$status"
[ "$status" = ok ] || failed=1

# A form without the token of the session is refused.
status=ok
code="$(curl -sS -b "$tmp/jar" -o /dev/null -w '%{http_code}' -d nMesiStallo=99 "$base/FileToBeExecuted/Config.php")"
[ "$code" = 400 ] || status="FAIL answered $code"
[ "$(sql 'SELECT nMesiStallo FROM Stallo')" != 99 ] || status="FAIL the form was applied"
report "form without the token" "$status"

# Configuration: every field is sent back as it is, except one rule of the grid.
curl -sS -b "$tmp/jar" -o "$tmp/form" "$base/FileToBeExecuted/Config.php"
args=()
while IFS= read -r field; do
    [ "${field%%=*}" = 'Regola[minors_more_adults]' ] && field='Regola[minors_more_adults]=12'
    args+=(--data-urlencode "$field")
done < <(perl -ne 'while (/<input[^>]*\bname=\x27([^\x27]+)\x27[^>]*>/g) { $tag = $&; $name = $1; $value = $tag =~ /\bvalue=\x27([^\x27]*)\x27/ ? $1 : ""; print "$name=$value\n" unless $name =~ /Psw|Aggiorna/ }' "$tmp/form")
curl -sS -b "$tmp/jar" -o "$tmp/body" "${args[@]}" "$base/FileToBeExecuted/Config.php"
status=ok
stored="$(sql "SELECT CONCAT_WS(' ', COUNT(*), SUM(Chiave='minors_more_adults' AND Punti=12), SUM(Chiave='minors_one_adult' AND Punti=15)) FROM RegolePunteggio")"
[ "$stored" = '24 1 1' ] || status="FAIL rules stored '$stored', expected '24 1 1'"
[ "$(sql "SELECT COUNT(*) FROM IndicatoriISEE WHERE Punteggio IN (100, 80, 60, 40, 20)")" = 5 ] || status="FAIL ISEE brackets changed"
if grep -qE '<b>(Fatal error|Parse error)</b>' "$tmp/body"; then status="FAIL fatal error"; fi
printf '%-46s %s\n' "configuration" "$status"
[ "$status" = ok ] || failed=1

# The update form comes filled with what is stored.
open_form 'Domanda Di Modifica'
status=ok
for expected in \
    'name="CognomeRichiedente" maxlength=.30. value="Smoke"' \
    'name="DataNascitaRichiedente" value="20/04/1980"' \
    "name='DataNascita\[3\]' value=\"30/12/$minor_year\"" \
    "<option value='Coniuge' selected>" \
    "<option value='Popolare' selected>" \
    "name='Anno\[7\]' value=\"2025\"" \
    "name='ValoreENT\[5\]' value=\"500\"" \
    "name='InvaliditaGraveMinori' min='0' max='22' value='1'" \
    "<option value='4' selected>4</option>"; do
    grep -q "$expected" "$tmp/form" || status="FAIL not filled in: $expected"
done
if grep -qE '<b>(Fatal error|Parse error|Warning)</b>' "$tmp/form"; then status="FAIL PHP error"; fi
printf '%-46s %s\n' "update form" "$status"
[ "$status" = ok ] || failed=1

# Update: higher ISEE, an adult with total disability, no other debts. The
# minors score follows the rule just changed; the members are still three.
submit_application 'Domanda Di Modifica' IndiceISEE=4000 InvaliditaTotaleAdulti=1 AltriDebiti=0
check 'updated application' '60 12 15 20 5 -22 90' '1 0 1 0 2'
status=ok
stored="$(sql "SELECT CONCAT_WS(' ', R.ISEE, (SELECT COUNT(*) FROM Entrate WHERE CodFamiglia='$tax_code'), (SELECT COUNT(*) FROM SituazioneSocioSanitaria WHERE CodFamiglia='$tax_code')) FROM Richiedente R WHERE R.Cod_Fiscale='$tax_code'")"
[ "$stored" = '4000 1 1' ] || status="FAIL applicant and rows stored '$stored', expected '4000 1 1'"
printf '%-46s %s\n' "updated rows" "$status"
[ "$status" = ok ] || failed=1

# post <page> field=value ...: sends a form of the session to a page.
post() {
    local page="$1" field args=(--data-urlencode "csrf=$csrf")
    shift
    for field in "$@"; do args+=(--data-urlencode "$field"); done
    curl -sS -b "$tmp/jar" -o "$tmp/body" "${args[@]}" "$base/FileToBeExecuted/$page"
}
no_php_error() {
    if grep -qE '<b>(Fatal error|Parse error)</b>' "$tmp/body"; then
        status="FAIL $(grep -oE '<b>(Fatal error|Parse error)</b>:[^<]*' "$tmp/body" | head -1 | sed -E 's/<[^>]+>//g')"
    fi
}

# Shopping cards: add one, assign it, look at the applicant, withdraw it, delete it.
card=SMOKECARD001
status=ok
post HandlePass.php "Codice=$card"
[ "$(sql "SELECT COUNT(*) FROM Tessera WHERE Codice='$card'")" = 1 ] || status="FAIL card not added"
no_php_error
report "card added" "$status"

status=ok
post Pass.php "Cod_Fiscale=$tax_code" "CodiceTessera=$card" Assegna=Assegna
stored="$(sql "SELECT CONCAT_WS(' ', (SELECT COUNT(*) FROM TesseraRilasciata WHERE CodFamiglia='$tax_code' AND CodiceTessera='$card' AND DataOraScadenzaTessera > DataOraRilascioTessera), (SELECT COUNT(*) FROM StoricoTessere WHERE CodFamiglia='$tax_code' AND CodiceTessera='$card' AND DataRitiro IS NULL))")"
[ "$stored" = '1 1' ] || status="FAIL assignment and history stored '$stored', expected '1 1'"
no_php_error
report "card assigned" "$status"

status=ok
post InformazioniRichiedente.php "Cod_Fiscale=$tax_code" Visualizza=Visualizza
grep -q 'Smoke' "$tmp/body" && grep -q 'Via Esempio 2' "$tmp/body" && grep -q 'Smoke Test' "$tmp/body" || status="FAIL applicant, address or members not shown"
no_php_error
report "applicant details" "$status"

status=ok
post PassRetire.php "Cod_Fiscale=$tax_code" Ritira=Ritira
stored="$(sql "SELECT CONCAT_WS(' ', (SELECT COUNT(*) FROM TesseraRilasciata WHERE CodFamiglia='$tax_code'), (SELECT COUNT(*) FROM StoricoTessere WHERE CodFamiglia='$tax_code' AND DataRitiro IS NOT NULL), (SELECT COUNT(*) FROM Richiedente WHERE Cod_Fiscale='$tax_code' AND DataOraStalloTessera >= DataOraRitiroTessera))")"
[ "$stored" = '0 1 1' ] || status="FAIL card, history and applicant stored '$stored', expected '0 1 1'"
no_php_error
report "card withdrawn" "$status"

status=ok
post HandlePass.php "Elimina=$card"
[ "$(sql "SELECT COUNT(*) FROM Tessera WHERE Codice='$card'")" = 0 ] || status="FAIL card not deleted"
no_php_error
report "card deleted" "$status"

# Text typed in a form is shown as text, not as markup.
submit_application 'Domanda Di Modifica' 'CognomeRichiedente=<i>Smoke</i>' 'Nome[1]=<script>alert(1)</script>'
status=ok
for page in List.php SkillsList.php StrikeOut.php Pass.php; do
    curl -sS -b "$tmp/jar" -o "$tmp/body" "$base/FileToBeExecuted/$page"
    if grep -qiE '<i>smoke</i>|<script>alert\(1\)' "$tmp/body"; then status="FAIL markup from the database printed by $page"; fi
done
curl -sS -b "$tmp/jar" -o "$tmp/body" "$base/FileToBeExecuted/List.php"
grep -qi '&lt;i&gt;smoke&lt;/i&gt;' "$tmp/body" || status="FAIL escaped surname not shown in the ranking"
post InformazioniRichiedente.php "Cod_Fiscale=$tax_code" Visualizza=Visualizza
if grep -qiE '<i>smoke</i>|<script>alert\(1\)' "$tmp/body"; then status="FAIL markup printed by the applicant details"; fi
report "markup in the data is escaped" "$status"

# Removing an applicant: a wildcard or a piece of SQL removes nobody.
applicants="$(sql 'SELECT COUNT(*) FROM Richiedente')"
status=ok
for attack in '%' "x' OR '1'='1" "$tax_code' OR '1'='1"; do
    post StrikeOut.php "Cod_Fiscale=$attack" Elimina=Elimina
    [ "$(sql 'SELECT COUNT(*) FROM Richiedente')" = "$applicants" ] || status="FAIL '$attack' removed applicants"
    no_php_error
done
report "removal with a forged code" "$status"

status=ok
post StrikeOut.php "Cod_Fiscale=$tax_code" Elimina=Elimina
[ "$(sql 'SELECT COUNT(*) FROM Richiedente')" = "$((applicants - 1))" ] || status="FAIL applicant not removed"
[ "$(sql "SELECT COUNT(*) FROM Membri WHERE CodFamiglia='$tax_code'")" = 0 ] || status="FAIL members left behind"
no_php_error
report "applicant removed" "$status"

exit "$failed"

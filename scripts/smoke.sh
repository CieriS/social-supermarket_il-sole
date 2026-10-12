#!/usr/bin/env bash
# Logs in with the demo account and opens every page, failing on HTTP errors,
# PHP fatal errors or a bounce back to the login page. Then submits a new
# application (with mistakes, then right, then twice), changes a rule of the
# grid in the configuration and submits an update, checking what ends up in
# the database each time.
# PHP warnings are reported but tolerated unless STRICT=1.
set -euo pipefail

cd "$(dirname "$0")/.."
base="${BASE_URL:-http://localhost:4330}"
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
failed=0

field() { grep -oE "name=\"$1\"[^>]*value=\"[^\"]*\"" index.php | sed -E 's/.*value="([^"]*)"/\1/'; }

curl -sS -c "$tmp/jar" -o "$tmp/login" \
    --data-urlencode "username=$(field username)" \
    --data-urlencode "psw=$(field psw)" \
    "$base/index.php"
if ! grep -q CookieUser "$tmp/jar"; then
    echo "FAIL login: no session cookie" >&2
    exit 1
fi

for page in index.php home.html FileToBeExecuted/*.php; do
    # Needs the choices made in Domanda.php: covered by the flows below.
    [ "$page" = FileToBeExecuted/ModuloDomanda.php ] && continue
    code="$(curl -sS -b "$tmp/jar" -o "$tmp/body" -w '%{http_code}' "$base/$page")"
    warnings="$(grep -cE '<b>(Warning|Notice|Deprecated)</b>' "$tmp/body" || true)"
    status=ok
    [ "$code" = 200 ] || status="FAIL http $code"
    if grep -qE '<b>(Fatal error|Parse error)</b>' "$tmp/body"; then status="FAIL fatal error"; fi
    if grep -q "location.replace('../index.php')" "$tmp/body"; then status="FAIL sent back to login"; fi
    if [ "${STRICT:-0}" = 1 ] && [ "$warnings" != 0 ]; then status="FAIL $warnings warnings"; fi
    printf '%-46s %-24s warnings=%s\n' "$page" "$status" "$warnings"
    [ "$status" = ok ] || failed=1
done

tax_code=SMKTST80A01A944Z
sql() { docker compose exec -T db mariadb -N -uroot -pdev EmporioSolidale -e "$1"; }
# shellcheck disable=SC2329  # invoked by the trap
cleanup() {
    sql "DELETE FROM Richiedente WHERE Cod_Fiscale='$tax_code';
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
        --data-urlencode "TipoDiDomanda=$1" -d nComponentiNucleoFamiliare=3 \
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

exit "$failed"

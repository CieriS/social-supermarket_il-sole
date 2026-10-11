#!/usr/bin/env bash
# Logs in with the demo account and opens every page, failing on HTTP errors,
# PHP fatal errors or a bounce back to the login page. Then submits a new
# application through the form and checks the score stored in the database.
# PHP warnings are reported but tolerated unless STRICT=1.
set -euo pipefail

cd "$(dirname "$0")/.."
base="${BASE_URL:-http://localhost:4330}"
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT

field() { grep -oE "name=\"$1\"[^>]*value=\"[^\"]*\"" index.php | sed -E 's/.*value="([^"]*)"/\1/'; }

curl -sS -c "$tmp/jar" -o "$tmp/login" \
    --data-urlencode "username=$(field username)" \
    --data-urlencode "psw=$(field psw)" \
    "$base/index.php"
if ! grep -q CookieUser "$tmp/jar"; then
    echo "FAIL login: no session cookie" >&2
    exit 1
fi

failed=0
for page in index.php home.html FileToBeExecuted/*.php; do
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

# A new application: unemployed head of the family, working spouse, one minor,
# ISEE 2000, public housing, income support, rent arrears.
tax_code=SMKTST80A01A944Z
sql() { docker compose exec -T db mariadb -N -uroot -pdev EmporioSolidale -e "$1"; }
sql "DELETE FROM Richiedente WHERE Cod_Fiscale='$tax_code'"

curl -sS -b "$tmp/jar" -c "$tmp/jar" -o /dev/null \
    -d 'TipoDiDomanda=Domanda Nuova' -d nComponentiNucleoFamiliare=3 -d CodiceFiscale= \
    "$base/FileToBeExecuted/Domanda.php"
curl -sS -b "$tmp/jar" -o "$tmp/form" "$base/FileToBeExecuted/PunteggioNew.php"

minor_year=$(($(date +%Y) - 5))
args=()
while IFS= read -r name; do
    case "$name" in
        CognomeRichiedente) value=Smoke ;;
        NomeRichiedente) value=Test ;;
        LuogoNascitaRichiedente | Residenza | 'LuogoNascita['[123]']') value=Bologna ;;
        DataNascitaRichiedente | 'DataNascita[1]') value=20/04/1980 ;;
        'DataNascita[2]') value=25/09/1982 ;;
        'DataNascita[3]') value="30/12/$minor_year" ;;
        Cittadinanza) value=ITA ;;
        Via) value='Via Esempio 2' ;;
        CodiceFiscale) value=$tax_code ;;
        TipoDiAlloggio) value=Popolare ;;
        DocumentoDIdent) value=AB1234567 ;;
        Telefono) value=3330000999 ;;
        Mail) value=smoke.test@example.com ;;
        ServiziSociali) value=No ;;
        TipoDiDomanda) value='Domanda Nuova' ;;
        nComponentiNucleoFamiliare) value=3 ;;
        IndiceISEE) value=2000 ;;
        'Nome['[123]']') value='Smoke Test' ;;
        'Parentela[1]') value=Capofamiglia ;;
        'Parentela[2]') value=Coniuge ;;
        'Parentela[3]') value=Figlio ;;
        'Occupazione[1]') value=Disoccupato ;;
        'Occupazione[2]') value=Altro ;;
        'Anno[7]') value=2025 ;;
        'ValoreENT[5]') value=500 ;;
        *) value= ;;
    esac
    args+=(--data-urlencode "$name=$value")
done < <(grep -oE "name=['\"][^'\"]+['\"]" "$tmp/form" | sed -E "s/name=['\"]([^'\"]+)['\"]/\1/" | sort -u)

curl -sS -b "$tmp/jar" -o "$tmp/body" "${args[@]}" "$base/FileToBeExecuted/PunteggioNew.php"

# ISEE, minors, unemployment, disability, debts, benefits, total
expected='80 10 15 0 5 -20 90'
actual="$(sql "SELECT CONCAT_WS(' ', PunteggioISEE, PunteggioPresenzaMinori, PunteggioDisoccupazione, PunteggioInvalidi, PunteggioSituazioneDebitoria, PunteggioBenefit, PunteggioTotale) FROM Punteggio WHERE CodFamiglia='$tax_code'")"
members="$(sql "SELECT CONCAT_WS(' ', COUNT(*), MIN(DataNascita)) FROM Membri WHERE CodFamiglia='$tax_code'")"
sql "DELETE FROM Richiedente WHERE Cod_Fiscale='$tax_code'"

status=ok
[ "$members" = "3 1980-04-20" ] || status="FAIL members stored '$members', expected '3 1980-04-20'"
[ "$actual" = "$expected" ] || status="FAIL scores '$actual', expected '$expected'"
if grep -qE '<b>(Fatal error|Parse error)</b>' "$tmp/body"; then status="FAIL fatal error"; fi
printf '%-46s %s\n' "new application" "$status"
[ "$status" = ok ] || failed=1

exit "$failed"

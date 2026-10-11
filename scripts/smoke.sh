#!/usr/bin/env bash
# Logs in with the demo account and opens every page, failing on HTTP errors,
# PHP fatal errors or a bounce back to the login page.
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
exit "$failed"

#!/usr/bin/env bash
# Builds dist/il-sole.zip: the files a host needs, in a folder ready to be
# uploaded, plus the three database files to import. Nothing else of the
# repository goes online. See docs/deploy.md.
set -euo pipefail

cd "$(dirname "$0")/.."
folder="${1:-il-sole}"

rm -rf dist
mkdir -p "dist/$folder/db" dist/database

cp index.php home.html .htaccess config.example.php "dist/$folder/"
cp -R FileToBeExecuted FileInclude src "dist/$folder/"
# Demo mode creates the tables from the schema on an empty database and
# loads the other two again when it restores the data.
cp db/01-schema.sql db/02-configuration.sql db/03-demo-data.sql "dist/$folder/db/"
# To import by hand, in this order, when the database is created.
cp db/01-schema.sql db/02-configuration.sql db/03-demo-data.sql dist/database/

# Sent by every page in the X-Build header, to tell from outside what is online.
build="$(git rev-parse --short HEAD)$(git diff --quiet HEAD -- . || echo -dirty)"
printf "<?php\n\nreturn '%s';\n" "$build" > "dist/$folder/src/build.php"

find dist -name .DS_Store -delete
(cd dist && zip -qr "$folder.zip" "$folder")

echo "dist/$folder.zip   upload its content to the host (build $build)"
echo "dist/database/     import the three files, in order"

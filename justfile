# List the available recipes
default:
    @just --list

# Start the app on http://localhost:4330
up:
    docker compose up -d --build --wait

# Stop the app, keeping the database
down:
    docker compose down

# Drop the database and reload the demo data
reset-db:
    docker compose down -v
    docker compose up -d --build --wait

# Follow the web and database logs
logs:
    docker compose logs -f

# Syntax-check every PHP file
lint:
    docker compose run --rm --no-deps web sh -c 'find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l > /dev/null'

# Log in and open every page, failing on PHP fatal errors
smoke:
    ./scripts/smoke.sh

# Everything that must pass before a change is done (needs `just up`)
check: lint smoke

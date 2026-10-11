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

# Install the development dependencies (PHPUnit)
install:
    docker compose exec -T web composer install --no-interaction --no-progress

# Run the unit tests
test:
    docker compose exec -T web vendor/bin/phpunit

# Syntax-check every PHP file
lint:
    docker compose run --rm --no-deps web sh -c 'find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l > /dev/null'

# Log in, open every page and submit an application
smoke:
    ./scripts/smoke.sh

# Everything that must pass before a change is done (needs `just up`)
check: lint test smoke

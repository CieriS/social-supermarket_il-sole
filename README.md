# Il Sole — social supermarket ranking

Back-office web application for the volunteer who manages the ranking of the
families applying to the *Il Sole* social supermarket. Each application is
scored against the supermarket's evaluation grid
([docs/griglia-valutazione.pdf](docs/griglia-valutazione.pdf)); the ranking
decides who receives a shopping card and how many monthly points it carries.

It started in 2021 as a high-school final exam project, written in vanilla PHP,
JavaScript and CSS without any framework, and is being completed in the same
spirit. All the data in the repository is invented.

## Features

- Ranking of the applicants with the score broken down by criterion
- New application and application update forms, with automatic scoring
- Shopping cards: assign, withdraw, manage, history
- Applicant details, family members' skills, applicant removal
- E-mail to applicants, ranking export, scoring configuration

## Stack

PHP 8.3 on Apache, MariaDB 11, plain HTML/CSS/JavaScript. Docker Compose for
local development.

## Running it

Requires Docker and [just](https://github.com/casey/just).

| Command         | What it does                                           |
| --------------- | ------------------------------------------------------ |
| `just up`       | Build and start the app on <http://localhost:4330>     |
| `just down`     | Stop it, keeping the database                          |
| `just reset-db` | Drop the database and reload the demo data             |
| `just logs`     | Follow the web and database logs                       |
| `just install`  | Install the development dependencies (first time only) |
| `just test`     | Unit tests of the scoring rules                        |
| `just check`    | Syntax check, unit tests and smoke test                |

The login page is pre-filled with the demo administrator account.

## Layout

| Path                | Content                                              |
| ------------------- | ---------------------------------------------------- |
| `index.php`         | Login                                                |
| `FileToBeExecuted/` | One PHP page per feature                             |
| `FileInclude/`      | Shared PHP includes, stylesheets, scripts and images |
| `src/`              | Scoring rules and form reading, as pure functions    |
| `tests/`            | Unit tests (PHPUnit)                                 |
| `db/`               | Schema and demo data loaded on first start           |
| `docs/`             | Evaluation grid and E/R diagrams                     |
| `scripts/`          | Smoke test: every page, plus one application         |

## Scoring

The rules of the grid live in [src/Scoring.php](src/Scoring.php) as pure
functions, tested case by case in [tests/ScoringTest.php](tests/ScoringTest.php).
[src/ApplicationForm.php](src/ApplicationForm.php) turns the submitted form
into the structure they score. The only runtime dependency is PHP itself:
Composer is used for PHPUnit alone.

Where the grid can be read in more than one way, this is the reading taken:

- **Minors**: every adult of the household counts, partner or not; 15 points
  with one adult, 10 with more, plus 4 for each minor after the first.
- **Unemployment**: 10 points for every unemployed adult (5 if semi-employed),
  plus at most one of the other lines, the highest that applies. A single
  parent counts as such only if no other adult of the household works. "Other
  adults" are the adults other than the head of the family and the spouse, and
  the line is added once.
- **Disability**: "alone" means a household of one person.
- **Other debts (2–10) and other contributions (2–5)**: the operator picks the
  value inside the range.

From the form, debts are read from the events of the social and health table
that have a year (seizure, arrears, eviction notice or imminent eviction) and
from emergency housing; benefits from public housing and from the income rows
for care allowances and income support.

## Status

The application runs as it was handed in. Known gaps, in the order they are
being addressed:

1. The form does not ask for disabilities, other debts and other
   contributions yet, so those lines of the grid score nothing.
2. Queries are built by string concatenation, passwords are hashed with MD5 and
   some pages skip the login check: not safe to expose as is.
3. The new/update application pages duplicate each other and mix scoring, SQL
   and markup.

The database container compares table names case-insensitively and runs
without strict mode, and PHP runs with output buffering on, mirroring the XAMPP
setup the code was written for.

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
- One form for new, updated and repeated applications, with automatic scoring
- Shopping cards: assign, withdraw, manage, history
- Applicant details, family members' skills, applicant removal
- E-mail to applicants, ranking export, scoring configuration

## Stack

PHP 8.5 on Apache, MariaDB 11, plain HTML/CSS/JavaScript. Docker Compose for
local development.

## Running it

Requires Docker and [just](https://github.com/casey/just).

| Command         | What it does                                           |
| --------------- | ------------------------------------------------------ |
| `just up`       | Build and start the app on <http://localhost:4330>     |
| `just down`     | Stop it, keeping the database                          |
| `just reset-db` | Drop the database and reload schema and demo data      |
| `just logs`     | Follow the web and database logs                       |
| `just install`  | Install the development dependencies (first time only) |
| `just test`     | Unit tests of the scoring rules                        |
| `just check`    | Syntax check, unit tests and smoke test                |
| `just demo-data` | Regenerate the demo data from its script              |

The demo account is `emporiosolidale` / `IlSole`; the login page shows it when
`DEMO_LOGIN_HINT` is set, as it is in `docker-compose.yml`.

## Layout

| Path                | Content                                              |
| ------------------- | ---------------------------------------------------- |
| `index.php`         | Login                                                |
| `FileToBeExecuted/` | One PHP page per feature                             |
| `FileInclude/`      | Shared PHP includes, stylesheets, scripts and images |
| `src/`              | Scoring, rules, form reading and storage             |
| `tests/`            | Unit tests (PHPUnit)                                 |
| `db/`               | `01-schema.sql` (tables, views, configuration) and `02-demo-data.sql` |
| `docs/`             | Evaluation grid and E/R diagrams                     |
| `scripts/`          | Smoke test and generator of the demo data            |

## How the application form works

[ModuloDomanda.php](FileToBeExecuted/ModuloDomanda.php) is the only page for
new, updated and repeated applications. It does all its work before printing
anything, through the modules in `src/`:

| Module                  | Role                                                          |
| ----------------------- | ------------------------------------------------------------- |
| `Scoring.php`           | The rules of the grid, one pure function per criterion        |
| `Rules.php`             | The configurable points: reading, validation, labels          |
| `ApplicationForm.php`   | Submitted form → structure to score                           |
| `ApplicationRecord.php` | Submitted form → record to store, its checks, and back        |
| `AssessmentFields.php`  | Markup of the "Griglia di valutazione" section                |
| `ApplicationStore.php`  | Reads and writes a record (prepared statements, transaction)  |
| `Auth.php`, `Html.php`  | Login, form tokens, HTML escaping                             |
| `Input.php`, `Export.php` | Request values, export cells                                |

Everything is pure and covered by the unit tests in `tests/`, except
`ApplicationStore.php` and the session and database half of `Auth.php`, which
are exercised by the smoke test. The only runtime dependency is PHP itself: Composer is used for
PHPUnit alone.

A form with mistakes is not saved: it comes back filled in, with the list of
what to fix. An update keeps the shopping card and the skills linked to the
members.

## Access

Every page starts by loading `src/bootstrap.php` and calling
`IlSole\Auth\requireLogin()`, before printing anything. The login is a PHP
session (HTTP-only cookie, expiring after 30 minutes without requests);
passwords are stored with `password_hash`. Every form carries a token of the
session and a POST without it is refused.

Five wrong passwords from the same address within fifteen minutes lock the
login for that address until the window has passed.

What comes from a request is reduced to the expected shape by
`src/Input.php` (tax codes, card codes, e-mail addresses, numbers) and reaches
the database only as a parameter; what comes from the database is escaped
before it is printed (`IlSole\Html\escapeRow`). Cells of the export that a
spreadsheet would run as formulas are made inert (`src/Export.php`).

## Scoring

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
for care allowances and income support. The lines that cannot be worked out
from the rest of the form are asked in its "Griglia di valutazione" section:
how many adults have a total or a severe disability, how many minors a severe
one, the points for other debts and for other contributions.

The points of every line are stored in the `RegolePunteggio` table and edited
from the configuration page ([src/Rules.php](src/Rules.php) checks what is
typed there). A change applies to the applications entered from then on; the
"Ricalcola" button of the same page scores every stored application again
with the current points, ISEE brackets and ages.

## Demo data

`db/02-demo-data.sql` is generated by
[scripts/generate-demo-data.php](scripts/generate-demo-data.php): twenty
invented families pass through the same code as a submitted form, so what is
stored and how it is scored is what the application itself would produce.
Six of them hold a shopping card and one has just given it back. To change
the data, edit the script and run `just demo-data`, then `just reset-db`.

## Status

Known gaps:

1. The members outside the ISEE household, which the paper form lists, are
   not collected (issue #6).
2. Apart from the application form, the pages still mix queries, logic and
   markup, as they did in 2021; their queries are parameterised and what they
   print is escaped, but they are not covered by unit tests, only by the
   smoke test.
3. The export is a tab-separated file served with an `.xls` name.

Table and view names are written with the same casing everywhere, so the
application also runs on a database that tells `Tessera` from `tessera`, as
Linux hosts do.

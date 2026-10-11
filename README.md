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
| `just check`    | PHP syntax check and smoke test of every page          |

The login page is pre-filled with the demo administrator account.

## Layout

| Path                | Content                                              |
| ------------------- | ---------------------------------------------------- |
| `index.php`         | Login                                                |
| `FileToBeExecuted/` | One PHP page per feature                             |
| `FileInclude/`      | Shared PHP includes, stylesheets, scripts and images |
| `db/`               | Schema and demo data loaded on first start           |
| `docs/`             | Evaluation grid and E/R diagrams                     |
| `scripts/`          | Smoke test                                           |

## Status

The application runs as it was handed in. Known gaps, in the order they are
being addressed:

1. The disability and debt criteria of the grid are not scored yet.
2. Queries are built by string concatenation, passwords are hashed with MD5 and
   some pages skip the login check: not safe to expose as is.
3. The new/update application pages duplicate each other and mix scoring, SQL
   and markup.

The database container compares table names case-insensitively and PHP runs
with output buffering on, mirroring the XAMPP setup the code was written for.

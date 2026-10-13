# Deploying the demo

The application needs PHP 8.2 or later with the `mysqli` and `mbstring`
extensions, Apache (it relies on `.htaccess`) and a MySQL 8 or MariaDB
database. Nothing has to be built and nothing is installed with Composer:
PHPUnit is for development only. The development stack and the CI run on
MySQL 8.0.

`just package` puts in `dist/` exactly what is described below: a zip with the
files for the host and a folder with the database files to import.

## What goes on the host

Everything the pages use:

- `index.php`, `home.html`, `.htaccess`
- `FileToBeExecuted/`, `FileInclude/`, `src/`
- `db/02-configuration.sql` and `db/03-demo-data.sql` (demo mode loads them
  again when it restores the data)

Not needed there: `tests/`, `scripts/`, `docker/`, `docs/`, `vendor/`, the
Docker and Composer files. If they are uploaded anyway, `.htaccess` keeps them
from being served.

## Steps

1. **Database**: use an empty database (if it holds tables of an older
   version, export and drop them first: the import does not overwrite) and
   import, in this order,
   `db/01-schema.sql`, `db/02-configuration.sql`, `db/03-demo-data.sql`
   (phpMyAdmin: *Import*, one file at a time).
2. **Files**: upload the files above to the web root.
3. **Settings**: copy `config.example.php` to `config.php` next to `index.php`
   and fill in the database host, user, password and name. On a host that has
   environment variables, set those instead: they win over the file.
4. **Check**: open the site. The login page shows the demo account and a
   button that fills it in.

Then, from another browser, make sure these answer "not found":
`/config.php`, `/db/01-schema.sql`, `/src/Auth.php`, `/.htaccess`.
If they do not, the host is ignoring `.htaccess` and the site must not stay
online like that.

## In a folder of an existing site

The application does not have to be at the web root: upload it to a folder
(for example `il-sole/`) and open that address. Its links are relative and
`.htaccess` protects the files wherever the folder is.

If the site redirects every address somewhere else, the folder is redirected
too. The redirect has to be narrowed to the home page only, for example in the
`.htaccess` of the web root:

```apache
RedirectMatch 301 ^/$ https://example.com/
```

## Checking what is online

Every page sends an `X-Build` header with the commit the package was built
from (`curl -sI <address> | grep -i x-build`). If a page is empty, set
`SHOW_ERRORS` to `1` in `config.php`, read the error, and turn it off again.

## Settings

| Name                 | Meaning                                                                 |
| -------------------- | ----------------------------------------------------------------------- |
| `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` | Database connection                           |
| `DEMO_MODE`          | `1` for a public demonstration (see below)                              |
| `DEMO_RESET_MINUTES` | How old the last reset must be before the next one; default 60          |
| `SHOW_ERRORS`        | `1` only while setting up: a fatal error is printed instead of an empty page |
| `DEMO_LOGIN_HINT`    | `username / password` shown on the login page with a "Compila" button   |

## Demo mode

Anyone with the demo account can change everything, so with `DEMO_MODE` on:

- **No mail leaves the server.** The mail page goes through its steps and
  says that nothing was sent.
- **The credentials cannot be changed**, so nobody can lock the others out.
- **Data and configuration are put back** as they are in `db/` when the login
  page is opened and the last reset is older than `DEMO_RESET_MINUTES`. No
  scheduled job is needed.

Card dates in the demo data are relative to the day they are loaded, and the
stored scores use the ages of the day the file was generated: run
`just demo-data` before a deploy to refresh them, or press "Ricalcola" in the
configuration page.

## Without demo mode

Leave `DEMO_MODE` and `DEMO_LOGIN_HINT` empty and do not import
`db/03-demo-data.sql`. There is then no account: create one row in
`Administrator` with a `password_hash()` value, for example with
`php -r 'echo password_hash("the password", PASSWORD_DEFAULT);'`.
Mail is sent with PHP's `mail()`, which needs a mail server configured on the
host.

<?php

/*
    Loaded at the very top of every page, before any output: opens the
    database connection ($connection) and the session.
*/

declare(strict_types=1);

require_once __DIR__ . '/Config.php';

/* Which build is answering: written by scripts/package.sh, absent in development */
$build = is_file(__DIR__ . '/build.php') ? (string) (require __DIR__ . '/build.php') : 'dev';
header('X-Build: ' . preg_replace('/[^A-Za-z0-9._-]/', '', $build));

/*
    SHOW_ERRORS, for the first minutes on a new host only: a fatal error is
    printed (message, file name, line) instead of leaving an empty page.
*/
if (\IlSole\Config\isOn(\IlSole\Config\setting('SHOW_ERRORS'))) {
    register_shutdown_function(static function (): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            if (!headers_sent()) {
                header('Content-Type: text/plain; charset=UTF-8');
            }
            echo "\nIl Sole, errore: {$error['message']}\nin " . basename($error['file']) . " riga {$error['line']}\n";
        }
    });
}

require_once __DIR__ . '/Html.php';
require_once __DIR__ . '/Views.php';
require_once __DIR__ . '/Input.php';
require_once __DIR__ . '/Auth.php';
require __DIR__ . '/../FileInclude/FileRequired/Connessione.php';

\IlSole\Auth\startSession();

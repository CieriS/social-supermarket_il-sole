<?php

/*
    Loaded at the very top of every page, before any output: opens the
    database connection ($connection) and the session.
*/

declare(strict_types=1);

require_once __DIR__ . '/Html.php';
require_once __DIR__ . '/Auth.php';
require __DIR__ . '/../FileInclude/FileRequired/Connessione.php';

\IlSole\Auth\startSession();

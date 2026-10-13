<?php

/*
    Settings for a host without environment variables: copy this file to
    config.php, next to index.php, and fill it in. config.php is not tracked
    by git. On Docker the same names are set in docker-compose.yml instead.
*/

return [
    'DB_HOST' => 'localhost',
    'DB_USER' => '',
    'DB_PASSWORD' => '',
    'DB_NAME' => '',

    /* Public demonstration: no real mail, fixed credentials, data restored every DEMO_RESET_MINUTES */
    'DEMO_MODE' => '1',
    'DEMO_RESET_MINUTES' => '60',

    /* Shown on the login page with a button that fills it in: "username / password" */
    'DEMO_LOGIN_HINT' => 'emporiosolidale / IlSole',
];

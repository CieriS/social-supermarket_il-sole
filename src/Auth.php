<?php

declare(strict_types=1);

namespace IlSole\Auth;

use mysqli;

/* The operator is logged in when the session cookie matches the one stored at login */
function isLoggedIn(mysqli $db, array $cookies): bool
{
    $cookie = $cookies['CookieUser'] ?? '';
    if (!is_string($cookie) || $cookie === '') {
        return false;
    }
    $row = $db->execute_query('SELECT 1 FROM Administrator WHERE Cookie = ?', [$cookie])->fetch_row();
    return $row !== null;
}

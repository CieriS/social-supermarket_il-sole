<?php

/*
    Login of the operator: a PHP session that expires after a while without
    requests, and a token that every form must send back.
*/

declare(strict_types=1);

namespace IlSole\Auth;

use mysqli;

use function IlSole\Html\e;

/* Seconds without requests after which the operator has to log in again */
const SESSION_TTL = 1800;

/* --- Pure checks --- */

function sessionIsValid(array $session, int $now): bool
{
    $operator = $session['operator'] ?? null;
    $lastSeen = $session['lastSeen'] ?? null;
    return is_string($operator) && $operator !== ''
        && is_int($lastSeen) && $lastSeen <= $now && $now - $lastSeen <= SESSION_TTL;
}

function csrfMatches(array $session, mixed $token): bool
{
    $expected = $session['csrf'] ?? null;
    return is_string($expected) && $expected !== '' && is_string($token) && hash_equals($expected, $token);
}

/* --- Session and database --- */

function startSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
    ]);
    session_start();
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
}

/* The hidden field every form includes */
function csrfField(): string
{
    return "<input type='hidden' name='csrf' value='" . e($_SESSION['csrf'] ?? '') . "'/>";
}

function logIn(mysqli $db, string $username, string $password): bool
{
    $row = $db->execute_query(
        'SELECT Username, Password FROM Administrator WHERE Username = ?',
        [strtolower(trim($username))]
    )->fetch_assoc();

    /* Verifying against a fixed hash keeps the answer equally slow for unknown usernames */
    $stored = $row['Password'] ?? '$2y$12$lOh.w0lc9/iDBsutYQZCL.8zQYfWZukW1X85GoeQco1XZfIr6vuU6';
    if (!password_verify($password, $stored) || $row === null) {
        return false;
    }

    if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
        $db->execute_query(
            'UPDATE Administrator SET Password = ? WHERE Username = ?',
            [password_hash($password, PASSWORD_DEFAULT), $row['Username']]
        );
    }

    session_regenerate_id(true);
    $_SESSION['operator'] = $row['Username'];
    $_SESSION['lastSeen'] = time();
    return true;
}

function logOut(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* Stops the request unless it carries the token of the session */
function requireCsrfToken(): void
{
    if (!csrfMatches($_SESSION, $_POST['csrf'] ?? null)) {
        http_response_code(400);
        exit('Richiesta non valida: ricaricare la pagina e riprovare.');
    }
}

/*
    First thing every page behind the login does: sends whoever is not logged
    in to the login page and refuses forms without the token of the session.
*/
function requireLogin(string $loginUrl = '../index.php'): void
{
    if (!sessionIsValid($_SESSION, time())) {
        logOut();
        header("Location: $loginUrl");
        exit;
    }
    $_SESSION['lastSeen'] = time();

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        requireCsrfToken();
    }
}

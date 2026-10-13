<?php

declare(strict_types=1);

namespace IlSole\Mail;

use function IlSole\Config\demoMode;

/* Sends a message, except in demo mode, where nothing ever leaves the server */
function send(string $to, string $subject, string $message, string $headers): bool
{
    if (demoMode() || $to === '') {
        return false;
    }
    return mail($to, $subject, $message, $headers);
}

<?php

/*
    Values read from a request, reduced to what the pages expect. Anything
    else becomes an empty string (or null), which matches nothing.
*/

declare(strict_types=1);

namespace IlSole\Input;

function text(mixed $value): string
{
    return is_scalar($value) ? trim((string) $value) : '';
}

/* Sixteen letters or digits, upper case */
function taxCode(mixed $value): string
{
    $code = strtoupper(text($value));
    return preg_match('/^[A-Z0-9]{16}$/', $code) ? $code : '';
}

/* Code of a shopping card: up to twelve letters or digits, upper case */
function cardCode(mixed $value): string
{
    $code = strtoupper(text($value));
    return preg_match('/^[A-Z0-9]{1,12}$/', $code) ? $code : '';
}

function email(mixed $value): string
{
    $address = text($value);
    return filter_var($address, FILTER_VALIDATE_EMAIL) === false ? '' : $address;
}

/* Text for a mail header: one line, so nothing can add headers of its own */
function singleLine(mixed $value): string
{
    return trim(preg_replace('/[\r\n\0]+/', ' ', text($value)));
}

/* A whole number between $min and $max, or null */
function wholeNumber(mixed $value, int $min, int $max): ?int
{
    $digits = text($value);
    if ($digits === '' || !ctype_digit($digits) || strlen($digits) > 9) {
        return null;
    }
    $number = (int) $digits;
    return $number >= $min && $number <= $max ? $number : null;
}

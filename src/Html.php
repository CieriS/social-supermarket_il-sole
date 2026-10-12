<?php

declare(strict_types=1);

namespace IlSole\Html;

/* Text made safe for an HTML attribute or element */
function e(mixed $value): string
{
    return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/*
    The <option> elements of a select: an empty one, then one per value, with
    $current selected. $labels gives the text of the values shown differently.
*/
function options(array $values, string $current, array $labels = []): string
{
    $html = "<option value=''></option>";
    foreach ($values as $value) {
        $selected = $value === $current ? ' selected' : '';
        $html .= "<option value='" . e($value) . "'$selected>" . ($labels[$value] ?? e($value)) . '</option>';
    }
    return $html;
}

/*
    A row read from the database with its text made safe for HTML; null stays
    null, so it can wrap the fetch call of a `while` loop.
*/
function escapeRow(?array $row): ?array
{
    return $row === null ? null : array_map(fn (mixed $value) => is_string($value) ? e($value) : $value, $row);
}

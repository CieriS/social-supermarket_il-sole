<?php

declare(strict_types=1);

namespace IlSole\Export;

/*
    A value as a cell of the tab-separated export: one line, no tabs, quotes
    doubled, and text that a spreadsheet would run as a formula made inert.
*/
function exportCell(mixed $value): string
{
    $cell = is_scalar($value) ? (string) $value : '';
    $cell = str_replace("\t", '\\t', $cell);
    $cell = preg_replace("/\r?\n|\r/", '\\n', $cell);
    if ($cell !== '' && !is_numeric($cell) && strpbrk($cell[0], '=+-@') !== false) {
        $cell = "'" . $cell;
    }
    if (str_contains($cell, '"')) {
        $cell = '"' . str_replace('"', '""', $cell) . '"';
    }
    return $cell;
}

function exportLine(array $values): string
{
    return implode("\t", array_map(exportCell(...), $values)) . "\n";
}

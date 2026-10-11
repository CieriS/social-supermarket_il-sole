<?php

/*
    The points of the evaluation grid as the operator configures them: reading
    the rows of the RegolePunteggio table, checking the values typed in the
    configuration page, and the Italian labels shown there.

    Pure: no database, no request.
*/

declare(strict_types=1);

namespace IlSole\Rules;

use InvalidArgumentException;

use function IlSole\Scoring\defaultRules;

/* Section of the grid => rule => label */
function ruleLabels(): array
{
    return [
        'Presenza di minori' => [
            'minors_one_adult' => '1 adulto con minore',
            'minors_more_adults' => '2 o più adulti con minore',
            'minors_each_other' => 'Ogni altro figlio minore',
        ],
        'Disoccupazione' => [
            'unemployed_adult' => 'Adulto disoccupato',
            'semi_employed_adult' => 'Adulto semioccupato',
            'single_parent_unemployed' => 'Mono genitore disoccupato con minori',
            'both_parents_unemployed' => 'Due genitori disoccupati con minori',
            'one_parent_of_two_unemployed' => 'Un genitore disoccupato su due con minori',
            'other_adults' => 'Altri adulti a carico',
        ],
        'Invalidità, inabilità o malattia grave' => [
            'disability_total_in_household' => 'Invalidità totale, in nucleo',
            'disability_total_alone' => 'Invalidità totale, da solo',
            'disability_severe_adult_in_household' => 'Invalidità grave di adulto, in nucleo',
            'disability_severe_adult_alone' => 'Invalidità grave di adulto, da solo',
            'disability_severe_minor' => 'Invalidità grave di minore',
        ],
        'Situazione debitoria' => [
            'debt_seizure' => 'Pignoramento di stipendio, pensione, C/C',
            'debt_arrears' => 'Morosità di affitto e/o utenze domestiche',
            'debt_eviction' => 'Sfratto o emergenza abitativa',
            'debt_other_min' => 'Altri debiti: minimo',
            'debt_other_max' => 'Altri debiti: massimo',
        ],
        'Benefit da dedurre' => [
            'benefit_public_housing' => 'Casa popolare',
            'benefit_income_support' => 'RES, REI, SIA',
            'benefit_care_allowance' => 'Accompagnamento',
            'benefit_other_min' => 'Contributi e sgravi: minimo',
            'benefit_other_max' => 'Contributi e sgravi: massimo',
        ],
    ];
}

/* A whole number of points, zero or more: '12' and 12 are, '1.5', '-1', '' are not */
function isPoints(mixed $value): bool
{
    return (is_int($value) && $value >= 0) || (is_string($value) && ctype_digit($value));
}

/*
    $rows: rows of the RegolePunteggio table, ['Chiave' => string, 'Punti' => int].
    Rules missing from the table, unknown or unreadable keep the value of the grid.
*/
function rulesFromRows(array $rows): array
{
    $rules = defaultRules();
    foreach ($rows as $row) {
        $key = $row['Chiave'] ?? null;
        $points = $row['Punti'] ?? null;
        if (is_string($key) && array_key_exists($key, $rules) && isPoints($points)) {
            $rules[$key] = (int) $points;
        }
    }
    return $rules;
}

/*
    $input: the rules typed in the configuration page, rule => points. Every
    rule of the grid must be there, as a whole number from 0 up, and the
    ranges must not be upside down. Anything else in $input is ignored.
*/
function rulesFromForm(mixed $input): array
{
    if (!is_array($input)) {
        throw new InvalidArgumentException('Punteggi della griglia mancanti');
    }

    $labels = array_merge(...array_values(ruleLabels()));
    $rules = [];
    foreach (array_keys(defaultRules()) as $key) {
        $points = $input[$key] ?? null;
        if (!isPoints($points)) {
            throw new InvalidArgumentException("\"{$labels[$key]}\" deve essere un numero intero da 0 in su");
        }
        $rules[$key] = (int) $points;
    }

    foreach (['debt_other' => 'Altri debiti', 'benefit_other' => 'Contributi e sgravi'] as $range => $label) {
        if ($rules["{$range}_min"] < 1) {
            throw new InvalidArgumentException("\"$label\": il minimo deve essere almeno 1");
        }
        if ($rules["{$range}_min"] > $rules["{$range}_max"]) {
            throw new InvalidArgumentException("\"$label\": il minimo non può superare il massimo");
        }
    }

    return $rules;
}

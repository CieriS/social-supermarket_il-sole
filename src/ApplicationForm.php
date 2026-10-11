<?php

/*
    Reads the application form (FileToBeExecuted/PunteggioNew.php and
    PunteggioChange.php) into the structure scored by src/Scoring.php.

    Pure: takes the submitted fields as an array, touches nothing else.
*/

declare(strict_types=1);

namespace IlSole\Form;

use function IlSole\Scoring\parseDate;

use const IlSole\Scoring\DISABILITY_SEVERE;
use const IlSole\Scoring\DISABILITY_TOTAL;

/* Rows of the "situazione socio-sanitaria" table of the form */
const EVENT_SEIZURE = 6;
const EVENT_ARREARS = 7;
const EVENT_EVICTION_NOTICE = 9;
const EVENT_EVICTION_IMMINENT = 10;

/* Rows of the "entrate" table of the form */
const INCOME_CARE_ALLOWANCE = 3;
const INCOME_SUPPORT = 5;

const HOUSING_PUBLIC = 'Popolare';
const HOUSING_EMERGENCY = 'Emergenza Abitativa';

/* The form sends free text in whatever case; options are stored capitalised */
function normalise(string $value): string
{
    return ucwords(strtolower(trim($value)));
}

/* One cell of a table of the form, '' when it is missing or not a plain value */
function cell(array $form, string $field, int $row): string
{
    $rows = $form[$field] ?? null;
    $value = is_array($rows) ? ($rows[$row] ?? '') : '';
    return is_scalar($value) ? trim((string) $value) : '';
}

function filled(array $form, string $field, int $row): bool
{
    return cell($form, $field, $row) !== '';
}

function positive(array $form, string $field, int $row): bool
{
    $value = cell($form, $field, $row);
    return is_numeric($value) && (float) $value > 0;
}

/* A date typed in the form (dd/mm/yyyy) as the database wants it, '' if unreadable */
function databaseDate(mixed $value): string
{
    $date = is_scalar($value) ? parseDate((string) $value) : null;
    return $date === null ? '' : $date->format('Y-m-d');
}

/* The fields of the "griglia di valutazione" section of the form, as stored in ValutazioneGriglia */
const ASSESSMENT_FIELDS = [
    'InvaliditaTotaleAdulti',
    'InvaliditaGraveAdulti',
    'InvaliditaGraveMinori',
    'AltriDebiti',
    'AltriContributi',
];

/* Whole numbers from 0 up; anything else (missing, text, negative) is 0 */
function assessmentFromForm(array $form): array
{
    $assessment = [];
    foreach (ASSESSMENT_FIELDS as $field) {
        $value = $form[$field] ?? '';
        $value = is_int($value) ? (string) $value : $value;
        $assessment[$field] = is_string($value) && ctype_digit(trim($value)) ? (int) trim($value) : 0;
    }
    return $assessment;
}

function applicationFromForm(array $form): array
{
    $housing = $form['TipoDiAlloggio'] ?? '';
    $size = max(0, (int) ($form['nComponentiNucleoFamiliare'] ?? 0));

    $members = [];
    for ($row = 1; $row <= $size; $row++) {
        $members[] = [
            'birthDate' => cell($form, 'DataNascita', $row),
            'relation' => normalise(cell($form, 'Parentela', $row)),
            'occupation' => normalise(cell($form, 'Occupazione', $row)),
        ];
    }

    $assessment = assessmentFromForm($form);
    $disabilities = array_merge(
        array_fill(0, $assessment['InvaliditaTotaleAdulti'], ['severity' => DISABILITY_TOTAL]),
        array_fill(0, $assessment['InvaliditaGraveAdulti'], ['severity' => DISABILITY_SEVERE]),
        array_fill(0, $assessment['InvaliditaGraveMinori'], ['severity' => DISABILITY_SEVERE, 'minor' => true]),
    );

    $isee = $form['IndiceISEE'] ?? 0;

    return [
        'isee' => is_numeric($isee) ? (float) $isee : 0.0,
        'members' => $members,
        'disabilities' => $disabilities,
        'debts' => [
            'seizure' => filled($form, 'Anno', EVENT_SEIZURE),
            'arrears' => filled($form, 'Anno', EVENT_ARREARS),
            'eviction' => filled($form, 'Anno', EVENT_EVICTION_NOTICE)
                || filled($form, 'Anno', EVENT_EVICTION_IMMINENT)
                || $housing === HOUSING_EMERGENCY,
            'other' => $assessment['AltriDebiti'],
        ],
        'benefits' => [
            'publicHousing' => $housing === HOUSING_PUBLIC,
            'incomeSupport' => positive($form, 'ValoreENT', INCOME_SUPPORT),
            'careAllowance' => positive($form, 'ValoreENT', INCOME_CARE_ALLOWANCE),
            'other' => $assessment['AltriContributi'],
        ],
    ];
}

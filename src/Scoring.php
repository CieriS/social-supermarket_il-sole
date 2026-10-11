<?php

/*
    Scoring of an application against the evaluation grid
    (docs/griglia-valutazione.pdf).

    Pure functions: no database, no request, no clock. Dates are passed in.
    Where the grid is ambiguous the reading taken is written next to the rule.
*/

declare(strict_types=1);

namespace IlSole\Scoring;

use DateTimeImmutable;
use InvalidArgumentException;

const UNEMPLOYED = 'Disoccupato';
const SEMI_EMPLOYED = 'Semioccupato';
const PARENT_RELATIONS = ['Capofamiglia', 'Coniuge'];

const DISABILITY_TOTAL = 'total';
const DISABILITY_SEVERE = 'severe';

/* Points of the grid, overridable one by one from the configuration */
function defaultRules(): array
{
    return [
        'minors_one_adult' => 15,
        'minors_more_adults' => 10,
        'minors_each_other' => 4,

        'unemployed_adult' => 10,
        'semi_employed_adult' => 5,
        'single_parent_unemployed' => 10,
        'both_parents_unemployed' => 10,
        'one_parent_of_two_unemployed' => 5,
        'other_adults' => 5,

        'disability_total_in_household' => 10,
        'disability_total_alone' => 20,
        'disability_severe_adult_in_household' => 5,
        'disability_severe_adult_alone' => 10,
        'disability_severe_minor' => 10,

        'debt_seizure' => 5,
        'debt_arrears' => 5,
        'debt_eviction' => 10,
        'debt_other_min' => 2,
        'debt_other_max' => 10,

        'benefit_public_housing' => 10,
        'benefit_income_support' => 10,
        'benefit_care_allowance' => 10,
        'benefit_other_min' => 2,
        'benefit_other_max' => 5,
    ];
}

/* Accepts the form format (dd/mm/yyyy) and the database one (yyyy-mm-dd) */
function parseDate(?string $value): ?DateTimeImmutable
{
    $value = trim((string) $value);
    foreach (['!d/m/Y', '!Y-m-d'] as $format) {
        $date = DateTimeImmutable::createFromFormat($format, $value);
        $errors = DateTimeImmutable::getLastErrors();
        if ($date !== false && ($errors === false || $errors['warning_count'] + $errors['error_count'] === 0)) {
            return $date;
        }
    }
    return null;
}

function ageOn(DateTimeImmutable $birthDate, DateTimeImmutable $today): int
{
    if ($birthDate > $today) {
        throw new InvalidArgumentException('Birth date is in the future');
    }
    return $birthDate->diff($today)->y;
}

/*
    A member is ['birthDate' => string, 'relation' => string, 'occupation' => string].
    The list includes the applicant. A member without a readable birth date is
    counted as an adult: the applicant always is one and the form asks for the
    date of everybody else.
*/
function isAdult(array $member, DateTimeImmutable $today): bool
{
    $birthDate = parseDate($member['birthDate'] ?? null);
    return $birthDate === null || ageOn($birthDate, $today) >= 18;
}

function countAdults(array $members, DateTimeImmutable $today): int
{
    return count(array_filter($members, fn (array $member) => isAdult($member, $today)));
}

function countMinors(array $members, DateTimeImmutable $today): int
{
    return count($members) - countAdults($members, $today);
}

/*
    $brackets: rows of the IndicatoriISEE table, ['ISEEmin', 'ISEEmax', 'Punteggio'].
    Upper bounds are inclusive; a value in the gap between two brackets
    (1500,50) falls in the higher one. Above the last bracket there are no points.
*/
function iseeScore(float $isee, array $brackets): int
{
    if ($isee < 0) {
        throw new InvalidArgumentException('ISEE cannot be negative');
    }
    usort($brackets, fn (array $a, array $b) => $a['ISEEmax'] <=> $b['ISEEmax']);
    foreach ($brackets as $bracket) {
        if ($isee <= $bracket['ISEEmax']) {
            return (int) $bracket['Punteggio'];
        }
    }
    return 0;
}

/* All the adults of the household count, partner or not */
function minorsScore(int $adults, int $minors, array $rules): int
{
    if ($minors < 1) {
        return 0;
    }
    $first = $adults <= 1 ? $rules['minors_one_adult'] : $rules['minors_more_adults'];
    return $first + $rules['minors_each_other'] * ($minors - 1);
}

/*
    "Adulto disoccupato" is added for every unemployed (or semi-employed)
    adult, then at most one of the other lines of the grid is added: the one
    worth the most among those that apply.

    - A single parent counts as such only if no other adult of the household
      works.
    - "One parent out of two" also covers a semi-employed parent.
    - "Other adults" are the adults who are neither the head of the family nor
      the spouse; the line is added once, however many they are.
*/
function unemploymentScore(array $members, DateTimeImmutable $today, array $rules): int
{
    $adults = array_values(array_filter($members, fn (array $member) => isAdult($member, $today)));
    $hasMinors = count($adults) < count($members);

    $occupationOf = fn (array $member) => $member['occupation'] ?? '';
    $isParent = fn (array $member) => in_array($member['relation'] ?? '', PARENT_RELATIONS, true);
    $countWith = fn (array $list, string $occupation) =>
        count(array_filter($list, fn (array $member) => $occupationOf($member) === $occupation));

    $score = $rules['unemployed_adult'] * $countWith($adults, UNEMPLOYED)
        + $rules['semi_employed_adult'] * $countWith($adults, SEMI_EMPLOYED);

    $parents = array_values(array_filter($adults, $isParent));
    $others = array_values(array_filter($adults, fn (array $member) => !$isParent($member)));
    $unemployedParents = $countWith($parents, UNEMPLOYED);
    $semiEmployedParents = $countWith($parents, SEMI_EMPLOYED);
    $workingOthers = count($others) - $countWith($others, UNEMPLOYED) - $countWith($others, SEMI_EMPLOYED);

    $extra = [0];
    if ($hasMinors && count($parents) === 1 && $unemployedParents === 1 && $workingOthers === 0) {
        $extra[] = $rules['single_parent_unemployed'];
    }
    if ($hasMinors && count($parents) === 2 && $unemployedParents === 2) {
        $extra[] = $rules['both_parents_unemployed'];
    }
    if ($hasMinors && count($parents) === 2 && $unemployedParents < 2 && $unemployedParents + $semiEmployedParents >= 1) {
        $extra[] = $rules['one_parent_of_two_unemployed'];
    }
    if (count($others) > 0) {
        $extra[] = $rules['other_adults'];
    }

    return $score + max($extra);
}

/*
    $cases: one entry per person, ['severity' => DISABILITY_TOTAL | DISABILITY_SEVERE,
    'minor' => bool]. "Alone" means a household of one person. The grid has no
    line for a minor with total disability: it is scored as the severe one.
*/
function disabilityScore(array $cases, int $householdSize, array $rules): int
{
    $alone = $householdSize <= 1;
    $score = 0;
    foreach ($cases as $case) {
        $severity = $case['severity'] ?? null;
        if (!in_array($severity, [DISABILITY_TOTAL, DISABILITY_SEVERE], true)) {
            throw new InvalidArgumentException('Unknown disability severity');
        }
        if (!empty($case['minor'])) {
            $score += $rules['disability_severe_minor'];
        } elseif ($severity === DISABILITY_TOTAL) {
            $score += $alone ? $rules['disability_total_alone'] : $rules['disability_total_in_household'];
        } else {
            $score += $alone ? $rules['disability_severe_adult_alone'] : $rules['disability_severe_adult_in_household'];
        }
    }
    return $score;
}

/* A value chosen by the operator inside a range of the grid; 0 means "does not apply" */
function withinRange(int $points, int $min, int $max): int
{
    if ($points === 0) {
        return 0;
    }
    if ($points < $min || $points > $max) {
        throw new InvalidArgumentException("Points must be 0 or between $min and $max");
    }
    return $points;
}

/*
    $debts: ['seizure' => bool, 'arrears' => bool, 'eviction' => bool, 'other' => int].
    'other' is the 2-10 line of the grid, chosen by the operator.
*/
function debtScore(array $debts, array $rules): int
{
    return (!empty($debts['seizure']) ? $rules['debt_seizure'] : 0)
        + (!empty($debts['arrears']) ? $rules['debt_arrears'] : 0)
        + (!empty($debts['eviction']) ? $rules['debt_eviction'] : 0)
        + withinRange((int) ($debts['other'] ?? 0), $rules['debt_other_min'], $rules['debt_other_max']);
}

/*
    $benefits: ['publicHousing' => bool, 'incomeSupport' => bool,
    'careAllowance' => bool, 'other' => int]. 'other' is the 2-5 line of the
    grid, chosen by the operator. The result is zero or negative.
*/
function benefitDeduction(array $benefits, array $rules): int
{
    return -1 * (
        (!empty($benefits['publicHousing']) ? $rules['benefit_public_housing'] : 0)
        + (!empty($benefits['incomeSupport']) ? $rules['benefit_income_support'] : 0)
        + (!empty($benefits['careAllowance']) ? $rules['benefit_care_allowance'] : 0)
        + withinRange((int) ($benefits['other'] ?? 0), $rules['benefit_other_min'], $rules['benefit_other_max'])
    );
}

/*
    Scores a whole application. Returns the six partial scores, under the names
    of the Punteggio table columns, and their total.
*/
function scoreApplication(array $application, array $iseeBrackets, DateTimeImmutable $today, ?array $rules = null): array
{
    $rules = array_merge(defaultRules(), $rules ?? []);
    $members = $application['members'] ?? [];

    $scores = [
        'PunteggioISEE' => iseeScore((float) ($application['isee'] ?? 0), $iseeBrackets),
        'PunteggioPresenzaMinori' => minorsScore(countAdults($members, $today), countMinors($members, $today), $rules),
        'PunteggioDisoccupazione' => unemploymentScore($members, $today, $rules),
        'PunteggioInvalidi' => disabilityScore($application['disabilities'] ?? [], count($members), $rules),
        'PunteggioSituazioneDebitoria' => debtScore($application['debts'] ?? [], $rules),
        'PunteggioBenefit' => benefitDeduction($application['benefits'] ?? [], $rules),
    ];
    $scores['PunteggioTotale'] = array_sum($scores);

    return $scores;
}

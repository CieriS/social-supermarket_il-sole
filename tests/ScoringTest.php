<?php

declare(strict_types=1);

namespace IlSole\Tests;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Scoring\ageOn;
use function IlSole\Scoring\benefitDeduction;
use function IlSole\Scoring\countAdults;
use function IlSole\Scoring\countMinors;
use function IlSole\Scoring\debtScore;
use function IlSole\Scoring\defaultRules;
use function IlSole\Scoring\disabilityScore;
use function IlSole\Scoring\iseeScore;
use function IlSole\Scoring\minorsScore;
use function IlSole\Scoring\parseDate;
use function IlSole\Scoring\scoreApplication;
use function IlSole\Scoring\unemploymentScore;

use const IlSole\Scoring\DISABILITY_SEVERE;
use const IlSole\Scoring\DISABILITY_TOTAL;

/* The expected values come from docs/griglia-valutazione.pdf */
final class ScoringTest extends TestCase
{
    private const TODAY = '2026-06-15';

    private const BRACKETS = [
        ['ISEEmin' => 0, 'ISEEmax' => 1500, 'Punteggio' => 100],
        ['ISEEmin' => 1501, 'ISEEmax' => 3000, 'Punteggio' => 80],
        ['ISEEmin' => 3001, 'ISEEmax' => 4500, 'Punteggio' => 60],
        ['ISEEmin' => 4501, 'ISEEmax' => 6000, 'Punteggio' => 40],
        ['ISEEmin' => 6001, 'ISEEmax' => 7500, 'Punteggio' => 20],
    ];

    private static function today(): DateTimeImmutable
    {
        return new DateTimeImmutable(self::TODAY);
    }

    private static function adult(string $relation, string $occupation = 'Altro'): array
    {
        return ['birthDate' => '01/01/1980', 'relation' => $relation, 'occupation' => $occupation];
    }

    private static function minor(): array
    {
        return ['birthDate' => '01/01/2015', 'relation' => 'Figlio', 'occupation' => ''];
    }

    /* Dates and ages */

    public function testParsesTheFormAndTheDatabaseDateFormats(): void
    {
        self::assertSame('2002-08-16', parseDate('16/08/2002')->format('Y-m-d'));
        self::assertSame('2002-08-16', parseDate('2002-08-16')->format('Y-m-d'));
        self::assertSame('2002-08-16', parseDate(' 16/08/2002 ')->format('Y-m-d'));
    }

    #[DataProvider('unreadableDates')]
    public function testUnreadableDatesAreNull(?string $value): void
    {
        self::assertNull(parseDate($value));
    }

    public static function unreadableDates(): array
    {
        return [[null], [''], ['yesterday'], ['31/02/2020'], ['2020-13-01'], ['16-08-2002']];
    }

    #[DataProvider('ages')]
    public function testAge(string $birthDate, int $expected): void
    {
        self::assertSame($expected, ageOn(new DateTimeImmutable($birthDate), self::today()));
    }

    public static function ages(): array
    {
        return [
            'born today' => ['2026-06-15', 0],
            'day before the 18th birthday' => ['2008-06-16', 17],
            'on the 18th birthday' => ['2008-06-15', 18],
            'day after the 18th birthday' => ['2008-06-14', 18],
            'leap day birth' => ['2008-02-29', 18],
        ];
    }

    public function testAgeRejectsABirthDateInTheFuture(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ageOn(new DateTimeImmutable('2026-06-16'), self::today());
    }

    public function testCountsAdultsAndMinors(): void
    {
        $members = [
            self::adult('Capofamiglia'),
            ['birthDate' => '15/06/2008', 'relation' => 'Figlio'],
            ['birthDate' => '16/06/2008', 'relation' => 'Figlio'],
            ['birthDate' => '', 'relation' => 'Altro'],
        ];

        self::assertSame(3, countAdults($members, self::today()));
        self::assertSame(1, countMinors($members, self::today()));
        self::assertSame(0, countAdults([], self::today()));
    }

    /* ISEE */

    #[DataProvider('iseeValues')]
    public function testIseeScore(float $isee, int $expected): void
    {
        self::assertSame($expected, iseeScore($isee, self::BRACKETS));
    }

    public static function iseeValues(): array
    {
        return [
            [0, 100], [1500, 100], [1500.5, 80], [1501, 80], [3000, 80], [3001, 60],
            [4500, 60], [4501, 40], [6000, 40], [6001, 20], [7500, 20], [7501, 0], [44444, 0],
        ];
    }

    public function testIseeScoreDoesNotDependOnTheOrderOfTheBrackets(): void
    {
        self::assertSame(60, iseeScore(4000, array_reverse(self::BRACKETS)));
    }

    public function testIseeScoreWithoutBracketsIsZero(): void
    {
        self::assertSame(0, iseeScore(100, []));
    }

    public function testIseeScoreRejectsANegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        iseeScore(-1, self::BRACKETS);
    }

    /* Minors */

    #[DataProvider('minorsCases')]
    public function testMinorsScore(int $adults, int $minors, int $expected): void
    {
        self::assertSame($expected, minorsScore($adults, $minors, defaultRules()));
    }

    public static function minorsCases(): array
    {
        return [
            'no minors' => [2, 0, 0],
            'one adult, one minor' => [1, 1, 15],
            'one adult, three minors' => [1, 3, 23],
            'two adults, one minor' => [2, 1, 10],
            'two adults, two minors' => [2, 2, 14],
            'another adult who is not the partner' => [3, 1, 10],
        ];
    }

    /* Unemployment */

    #[DataProvider('unemploymentCases')]
    public function testUnemploymentScore(array $members, int $expected): void
    {
        self::assertSame($expected, unemploymentScore($members, self::today(), defaultRules()));
    }

    public static function unemploymentCases(): array
    {
        $unemployed = 'Disoccupato';
        $semi = 'Semioccupato';

        return [
            'nobody' => [[], 0],
            'everybody works' => [[self::adult('Capofamiglia'), self::adult('Coniuge'), self::minor()], 0],
            'unemployed adult living alone' => [[self::adult('Capofamiglia', $unemployed)], 10],
            'semi-employed adult living alone' => [[self::adult('Capofamiglia', $semi)], 5],
            'single unemployed parent with a minor' => [[self::adult('Capofamiglia', $unemployed), self::minor()], 20],
            'single semi-employed parent with a minor' => [[self::adult('Capofamiglia', $semi), self::minor()], 5],
            'single unemployed parent without minors' => [[self::adult('Capofamiglia', $unemployed)], 10],
            'single unemployed parent with a working adult' => [
                [self::adult('Capofamiglia', $unemployed), self::adult('Altro'), self::minor()], 15,
            ],
            'single unemployed parent with an unemployed adult' => [
                [self::adult('Capofamiglia', $unemployed), self::adult('Altro', $unemployed), self::minor()], 30,
            ],
            'both parents unemployed with minors' => [
                [self::adult('Capofamiglia', $unemployed), self::adult('Coniuge', $unemployed), self::minor(), self::minor()], 30,
            ],
            'both parents unemployed without minors' => [
                [self::adult('Capofamiglia', $unemployed), self::adult('Coniuge', $unemployed)], 20,
            ],
            'one parent of two unemployed with minors' => [
                [self::adult('Capofamiglia'), self::adult('Coniuge', $unemployed), self::minor()], 15,
            ],
            'one parent of two semi-employed with minors' => [
                [self::adult('Capofamiglia', $semi), self::adult('Coniuge'), self::minor()], 10,
            ],
            'one unemployed and one semi-employed parent with minors' => [
                [self::adult('Capofamiglia', $unemployed), self::adult('Coniuge', $semi), self::minor()], 20,
            ],
            'only one extra line is added' => [
                [
                    self::adult('Capofamiglia', $unemployed), self::adult('Coniuge', $unemployed),
                    self::adult('Altro'), self::adult('Altro'), self::minor(),
                ],
                30,
            ],
            'working parents with other adults' => [
                [self::adult('Capofamiglia'), self::adult('Coniuge'), self::adult('Altro'), self::adult('Altro')], 5,
            ],
            'a minor marked as unemployed does not count' => [
                [self::adult('Capofamiglia'), ['birthDate' => '01/01/2015', 'relation' => 'Figlio', 'occupation' => $unemployed]], 0,
            ],
            'an adult child is another adult' => [
                [self::adult('Capofamiglia'), self::adult('Figlio', $unemployed)], 15,
            ],
        ];
    }

    /* Disability */

    #[DataProvider('disabilityCases')]
    public function testDisabilityScore(array $cases, int $householdSize, int $expected): void
    {
        self::assertSame($expected, disabilityScore($cases, $householdSize, defaultRules()));
    }

    public static function disabilityCases(): array
    {
        $total = ['severity' => DISABILITY_TOTAL];
        $severe = ['severity' => DISABILITY_SEVERE];

        return [
            'none' => [[], 3, 0],
            'total, in a household' => [[$total], 3, 10],
            'total, alone' => [[$total], 1, 20],
            'severe adult, in a household' => [[$severe], 2, 5],
            'severe adult, alone' => [[$severe], 1, 10],
            'severe minor' => [[$severe + ['minor' => true]], 3, 10],
            'total minor is scored as severe' => [[$total + ['minor' => true]], 3, 10],
            'several people' => [[$total, $severe, $severe + ['minor' => true]], 4, 25],
        ];
    }

    public function testDisabilityScoreRejectsAnUnknownSeverity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        disabilityScore([['severity' => 'mild']], 2, defaultRules());
    }

    /* Debts */

    #[DataProvider('debtCases')]
    public function testDebtScore(array $debts, int $expected): void
    {
        self::assertSame($expected, debtScore($debts, defaultRules()));
    }

    public static function debtCases(): array
    {
        return [
            'none' => [[], 0],
            'seizure' => [['seizure' => true], 5],
            'arrears' => [['arrears' => true], 5],
            'eviction' => [['eviction' => true], 10],
            'other, lowest' => [['other' => 2], 2],
            'other, highest' => [['other' => 10], 10],
            'everything' => [['seizure' => true, 'arrears' => true, 'eviction' => true, 'other' => 10], 30],
            'explicit no' => [['seizure' => false, 'other' => 0], 0],
        ];
    }

    #[DataProvider('otherDebtsOutOfRange')]
    public function testDebtScoreRejectsOtherDebtsOutOfTheGridRange(int $points): void
    {
        $this->expectException(InvalidArgumentException::class);
        debtScore(['other' => $points], defaultRules());
    }

    public static function otherDebtsOutOfRange(): array
    {
        return [[1], [11], [-2]];
    }

    /* Benefits */

    #[DataProvider('benefitCases')]
    public function testBenefitDeduction(array $benefits, int $expected): void
    {
        self::assertSame($expected, benefitDeduction($benefits, defaultRules()));
    }

    public static function benefitCases(): array
    {
        return [
            'none' => [[], 0],
            'public housing' => [['publicHousing' => true], -10],
            'income support' => [['incomeSupport' => true], -10],
            'care allowance' => [['careAllowance' => true], -10],
            'other, lowest' => [['other' => 2], -2],
            'other, highest' => [['other' => 5], -5],
            'everything' => [['publicHousing' => true, 'incomeSupport' => true, 'careAllowance' => true, 'other' => 5], -35],
        ];
    }

    #[DataProvider('otherBenefitsOutOfRange')]
    public function testBenefitDeductionRejectsOtherBenefitsOutOfTheGridRange(int $points): void
    {
        $this->expectException(InvalidArgumentException::class);
        benefitDeduction(['other' => $points], defaultRules());
    }

    public static function otherBenefitsOutOfRange(): array
    {
        return [[1], [6]];
    }

    /* Whole application */

    public function testScoresAWholeApplication(): void
    {
        $application = [
            'isee' => 2000,
            'members' => [
                self::adult('Capofamiglia', 'Disoccupato'),
                self::adult('Coniuge'),
                self::minor(),
                self::minor(),
            ],
            'disabilities' => [['severity' => DISABILITY_SEVERE, 'minor' => true]],
            'debts' => ['arrears' => true, 'other' => 4],
            'benefits' => ['publicHousing' => true],
        ];

        self::assertSame(
            [
                'PunteggioISEE' => 80,
                'PunteggioPresenzaMinori' => 14,
                'PunteggioDisoccupazione' => 15,
                'PunteggioInvalidi' => 10,
                'PunteggioSituazioneDebitoria' => 9,
                'PunteggioBenefit' => -10,
                'PunteggioTotale' => 118,
            ],
            scoreApplication($application, self::BRACKETS, self::today())
        );
    }

    public function testAnEmptyApplicationScoresOnlyTheLowestIseeBracket(): void
    {
        $scores = scoreApplication([], self::BRACKETS, self::today());

        self::assertSame(100, $scores['PunteggioISEE']);
        self::assertSame(100, $scores['PunteggioTotale']);
    }

    public function testRulesCanBeOverriddenOneByOne(): void
    {
        $application = ['isee' => 9000, 'members' => [self::adult('Capofamiglia'), self::minor()]];

        $scores = scoreApplication($application, self::BRACKETS, self::today(), ['minors_one_adult' => 20]);

        self::assertSame(20, $scores['PunteggioPresenzaMinori']);
        self::assertSame(20, $scores['PunteggioTotale']);
    }
}

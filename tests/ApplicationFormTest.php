<?php

declare(strict_types=1);

namespace IlSole\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Form\applicationFromForm;
use function IlSole\Form\databaseDate;
use function IlSole\Scoring\scoreApplication;

final class ApplicationFormTest extends TestCase
{
    public function testReadsTheMembersOfTheHousehold(): void
    {
        $application = applicationFromForm([
            'nComponentiNucleoFamiliare' => '3',
            'DataNascita' => [1 => '01/04/1988', 2 => '11/09/1990', 3 => '20/12/2015'],
            'Parentela' => [1 => 'capofamiglia', 2 => 'CONIUGE', 3 => 'Figlio'],
            'Occupazione' => [1 => 'disoccupato', 2 => 'Altro', 3 => ''],
        ]);

        self::assertSame(
            [
                ['birthDate' => '01/04/1988', 'relation' => 'Capofamiglia', 'occupation' => 'Disoccupato'],
                ['birthDate' => '11/09/1990', 'relation' => 'Coniuge', 'occupation' => 'Altro'],
                ['birthDate' => '20/12/2015', 'relation' => 'Figlio', 'occupation' => ''],
            ],
            $application['members']
        );
    }

    public function testIgnoresTheRowsBeyondTheDeclaredHouseholdSize(): void
    {
        $application = applicationFromForm([
            'nComponentiNucleoFamiliare' => '1',
            'DataNascita' => [1 => '01/04/1988', 2 => '11/09/1990'],
        ]);

        self::assertCount(1, $application['members']);
    }

    public function testFillsTheRowsTheFormDidNotSend(): void
    {
        $application = applicationFromForm(['nComponentiNucleoFamiliare' => '2']);

        self::assertSame(
            array_fill(0, 2, ['birthDate' => '', 'relation' => '', 'occupation' => '']),
            $application['members']
        );
    }

    #[DataProvider('householdSizes')]
    public function testAnUnusableHouseholdSizeMeansNoMembers(mixed $size): void
    {
        self::assertSame([], applicationFromForm(['nComponentiNucleoFamiliare' => $size])['members']);
    }

    public static function householdSizes(): array
    {
        return [[null], [''], ['-3'], ['abc'], [0]];
    }

    #[DataProvider('iseeValues')]
    public function testReadsTheIsee(mixed $value, float $expected): void
    {
        self::assertSame($expected, applicationFromForm(['IndiceISEE' => $value])['isee']);
    }

    public static function iseeValues(): array
    {
        return [['2500', 2500.0], ['2500.50', 2500.5], ['', 0.0], ['abc', 0.0], [null, 0.0]];
    }

    public function testAnEmptyFormHasNoDebtsAndNoBenefits(): void
    {
        $application = applicationFromForm([]);

        self::assertSame(['seizure' => false, 'arrears' => false, 'eviction' => false], $application['debts']);
        self::assertSame(
            ['publicHousing' => false, 'incomeSupport' => false, 'careAllowance' => false],
            $application['benefits']
        );
    }

    public function testEventRowsSentEmptyAreNotDebts(): void
    {
        $application = applicationFromForm(['Anno' => array_fill(1, 12, ''), 'DocAll' => array_fill(1, 12, '')]);

        self::assertSame(['seizure' => false, 'arrears' => false, 'eviction' => false], $application['debts']);
    }

    #[DataProvider('debtEvents')]
    public function testReadsTheDebtsFromTheEventsWithAYear(int $row, string $debt): void
    {
        $debts = applicationFromForm(['Anno' => [$row => '2020']])['debts'];

        self::assertSame([$debt], array_keys(array_filter($debts)));
    }

    public static function debtEvents(): array
    {
        return [[6, 'seizure'], [7, 'arrears'], [9, 'eviction'], [10, 'eviction']];
    }

    public function testOtherEventsAreNotDebts(): void
    {
        $debts = applicationFromForm(['Anno' => [1 => '2020', 8 => '2020', 11 => '2020', 12 => '2020']])['debts'];

        self::assertSame([], array_filter($debts));
    }

    public function testEmergencyHousingIsAnEviction(): void
    {
        self::assertTrue(applicationFromForm(['TipoDiAlloggio' => 'Emergenza Abitativa'])['debts']['eviction']);
    }

    public function testReadsTheBenefits(): void
    {
        $benefits = applicationFromForm([
            'TipoDiAlloggio' => 'Popolare',
            'ValoreENT' => [1 => '1200', 3 => '500', 5 => '0.01'],
        ])['benefits'];

        self::assertSame(['publicHousing' => true, 'incomeSupport' => true, 'careAllowance' => true], $benefits);
    }

    public function testEmptyZeroOrTextIncomesAreNotBenefits(): void
    {
        $benefits = applicationFromForm([
            'TipoDiAlloggio' => 'Affitto Privato',
            'ValoreENT' => [3 => '', 5 => '0', 4 => '900'],
        ])['benefits'];

        self::assertSame([], array_filter($benefits));
    }

    public function testSurvivesFieldsOfTheWrongShape(): void
    {
        $application = applicationFromForm([
            'nComponentiNucleoFamiliare' => '1',
            'DataNascita' => [1 => ['x']],
            'Parentela' => 'Capofamiglia',
            'Anno' => 'x',
            'ValoreENT' => [3 => ['x']],
        ]);

        self::assertSame([['birthDate' => '', 'relation' => '', 'occupation' => '']], $application['members']);
        self::assertSame([], array_filter($application['debts']));
        self::assertSame([], array_filter($application['benefits']));
    }

    #[DataProvider('formDates')]
    public function testConvertsAFormDateForTheDatabase(mixed $value, string $expected): void
    {
        self::assertSame($expected, databaseDate($value));
    }

    public static function formDates(): array
    {
        return [
            'day after the 12th' => ['20/04/1988', '1988-04-20'],
            'day before the 13th is not read as a month' => ['03/04/1988', '1988-04-03'],
            'already in database format' => ['1988-04-20', '1988-04-20'],
            'empty' => ['', ''],
            'not a date' => ['abc', ''],
            'impossible date' => ['31/02/2020', ''],
            'missing' => [null, ''],
            'not a plain value' => [['20/04/1988'], ''],
        ];
    }

    public function testAFormScoresEndToEnd(): void
    {
        $scores = scoreApplication(
            applicationFromForm([
                'IndiceISEE' => '2000',
                'TipoDiAlloggio' => 'Popolare',
                'nComponentiNucleoFamiliare' => '2',
                'DataNascita' => [1 => '01/04/1988', 2 => '20/12/2015'],
                'Parentela' => [1 => 'Capofamiglia', 2 => 'Figlio'],
                'Occupazione' => [1 => 'Disoccupato', 2 => ''],
                'Anno' => [7 => '2025'],
            ]),
            [['ISEEmin' => 1501, 'ISEEmax' => 3000, 'Punteggio' => 80]],
            new DateTimeImmutable('2026-06-15')
        );

        self::assertSame(
            [
                'PunteggioISEE' => 80,
                'PunteggioPresenzaMinori' => 15,
                'PunteggioDisoccupazione' => 20,
                'PunteggioInvalidi' => 0,
                'PunteggioSituazioneDebitoria' => 5,
                'PunteggioBenefit' => -10,
                'PunteggioTotale' => 110,
            ],
            $scores
        );
    }
}

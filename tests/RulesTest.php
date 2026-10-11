<?php

declare(strict_types=1);

namespace IlSole\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Rules\ruleLabels;
use function IlSole\Rules\rulesFromForm;
use function IlSole\Rules\rulesFromRows;
use function IlSole\Scoring\defaultRules;

final class RulesTest extends TestCase
{
    public function testEveryRuleHasExactlyOneLabel(): void
    {
        $labelled = array_merge(...array_map('array_keys', array_values(ruleLabels())));

        self::assertSame(array_keys(defaultRules()), $labelled);
    }

    public function testNoRowsMeansTheGrid(): void
    {
        self::assertSame(defaultRules(), rulesFromRows([]));
    }

    public function testRowsOverrideTheGrid(): void
    {
        $rules = rulesFromRows([
            ['Chiave' => 'minors_one_adult', 'Punti' => '20'],
            ['Chiave' => 'debt_eviction', 'Punti' => 0],
        ]);

        self::assertSame(20, $rules['minors_one_adult']);
        self::assertSame(0, $rules['debt_eviction']);
        self::assertSame(10, $rules['minors_more_adults']);
    }

    #[DataProvider('unusableRows')]
    public function testUnusableRowsAreIgnored(array $row): void
    {
        self::assertSame(defaultRules(), rulesFromRows([$row]));
    }

    public static function unusableRows(): array
    {
        return [
            'unknown rule' => [['Chiave' => 'bonus', 'Punti' => 5]],
            'negative' => [['Chiave' => 'debt_seizure', 'Punti' => -5]],
            'text' => [['Chiave' => 'debt_seizure', 'Punti' => 'abc']],
            'decimal' => [['Chiave' => 'debt_seizure', 'Punti' => '2.5']],
            'empty' => [['Chiave' => 'debt_seizure', 'Punti' => '']],
            'no points' => [['Chiave' => 'debt_seizure']],
            'no key' => [['Punti' => 5]],
            'key of the wrong type' => [['Chiave' => 3, 'Punti' => 5]],
        ];
    }

    public function testAcceptsTheGridAsTypedInTheForm(): void
    {
        $typed = array_map('strval', defaultRules());

        self::assertSame(defaultRules(), rulesFromForm($typed));
    }

    public function testIgnoresFieldsThatAreNotRules(): void
    {
        self::assertSame(defaultRules(), rulesFromForm(defaultRules() + ['bonus' => '50']));
    }

    public function testAcceptsZeroPointsAndARangeOfOneValue(): void
    {
        $rules = rulesFromForm(['debt_seizure' => '0', 'debt_other_min' => '3', 'debt_other_max' => '3'] + defaultRules());

        self::assertSame(0, $rules['debt_seizure']);
        self::assertSame(3, $rules['debt_other_max']);
    }

    #[DataProvider('rejectedForms')]
    public function testRejects(mixed $input, string $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        rulesFromForm($input);
    }

    public static function rejectedForms(): array
    {
        $grid = defaultRules();
        $missing = $grid;
        unset($missing['other_adults']);

        return [
            'nothing sent' => [null, 'mancanti'],
            'not a list of rules' => ['abc', 'mancanti'],
            'a rule is missing' => [$missing, 'Altri adulti a carico'],
            'negative' => [['debt_arrears' => '-1'] + $grid, 'Morosità'],
            'decimal' => [['minors_each_other' => '4.5'] + $grid, 'Ogni altro figlio minore'],
            'text' => [['unemployed_adult' => 'dieci'] + $grid, 'Adulto disoccupato'],
            'empty' => [['benefit_public_housing' => ''] + $grid, 'Casa popolare'],
            'nested' => [['benefit_public_housing' => ['10']] + $grid, 'Casa popolare'],
            'range upside down' => [['debt_other_min' => '8', 'debt_other_max' => '5'] + $grid, 'Altri debiti'],
            'range starting at zero' => [['benefit_other_min' => '0'] + $grid, 'Contributi e sgravi'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IlSole\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Form\applicationFromForm;
use function IlSole\Form\assessmentFieldsHtml;
use function IlSole\Form\assessmentFromForm;
use function IlSole\Scoring\defaultRules;

use const IlSole\Form\ASSESSMENT_FIELDS;
use const IlSole\Scoring\DISABILITY_SEVERE;
use const IlSole\Scoring\DISABILITY_TOTAL;

final class AssessmentFieldsTest extends TestCase
{
    private const NOTHING = [
        'InvaliditaTotaleAdulti' => 0,
        'InvaliditaGraveAdulti' => 0,
        'InvaliditaGraveMinori' => 0,
        'AltriDebiti' => 0,
        'AltriContributi' => 0,
    ];

    public function testAnEmptyFormAssessesNothing(): void
    {
        self::assertSame(self::NOTHING, assessmentFromForm([]));
    }

    public function testReadsTheAssessment(): void
    {
        $assessment = assessmentFromForm([
            'InvaliditaTotaleAdulti' => '1',
            'InvaliditaGraveAdulti' => ' 2 ',
            'InvaliditaGraveMinori' => 3,
            'AltriDebiti' => '10',
            'AltriContributi' => '5',
        ]);

        self::assertSame([1, 2, 3, 10, 5], array_values($assessment));
    }

    #[DataProvider('unusableValues')]
    public function testUnusableValuesAreZero(mixed $value): void
    {
        self::assertSame(self::NOTHING, assessmentFromForm(array_fill_keys(ASSESSMENT_FIELDS, $value)));
    }

    public static function unusableValues(): array
    {
        return [[''], ['-1'], ['1.5'], ['abc'], [null], [['1']], [-2], [1.0]];
    }

    public function testTheAssessmentReachesTheApplication(): void
    {
        $application = applicationFromForm([
            'InvaliditaTotaleAdulti' => '1',
            'InvaliditaGraveAdulti' => '2',
            'InvaliditaGraveMinori' => '1',
            'AltriDebiti' => '7',
            'AltriContributi' => '3',
        ]);

        self::assertSame(
            [
                ['severity' => DISABILITY_TOTAL],
                ['severity' => DISABILITY_SEVERE],
                ['severity' => DISABILITY_SEVERE],
                ['severity' => DISABILITY_SEVERE, 'minor' => true],
            ],
            $application['disabilities']
        );
        self::assertSame(7, $application['debts']['other']);
        self::assertSame(3, $application['benefits']['other']);
    }

    public function testWithoutAnAssessmentTheApplicationHasNone(): void
    {
        $application = applicationFromForm([]);

        self::assertSame([], $application['disabilities']);
        self::assertSame(0, $application['debts']['other']);
        self::assertSame(0, $application['benefits']['other']);
    }

    public function testRendersEveryFieldOnce(): void
    {
        $html = assessmentFieldsHtml([], defaultRules());

        foreach (ASSESSMENT_FIELDS as $field) {
            self::assertSame(1, substr_count($html, "name='$field'"), $field);
        }
    }

    public function testRendersTheStoredValues(): void
    {
        $html = assessmentFieldsHtml(
            ['InvaliditaTotaleAdulti' => '2', 'AltriDebiti' => '7', 'AltriContributi' => '5'],
            defaultRules()
        );

        self::assertStringContainsString("name='InvaliditaTotaleAdulti' min='0' max='22' value='2'", $html);
        self::assertStringContainsString("name='InvaliditaGraveAdulti' min='0' max='22' value='0'", $html);
        self::assertStringContainsString("<option value='7' selected>7</option>", $html);
        self::assertStringContainsString("<option value='5' selected>5</option>", $html);
        self::assertSame(2, substr_count($html, ' selected'));
    }

    public function testTheRangesFollowTheConfiguredRules(): void
    {
        $rules = ['debt_other_min' => 3, 'debt_other_max' => 4, 'benefit_other_min' => 1, 'benefit_other_max' => 1] + defaultRules();

        $html = assessmentFieldsHtml([], $rules);

        self::assertStringContainsString(
            "<select name='AltriDebiti'><option value='0'>No</option><option value='3'>3</option><option value='4'>4</option></select>",
            $html
        );
        self::assertStringContainsString(
            "<select name='AltriContributi'><option value='0'>No</option><option value='1'>1</option></select>",
            $html
        );
    }

    public function testAStoredValueOutsideTheRangeIsShownAsNo(): void
    {
        $html = assessmentFieldsHtml(['AltriDebiti' => '11'], defaultRules());

        self::assertStringNotContainsString(' selected', $html);
    }

    public function testValuesThatAreNotNumbersNeverReachTheMarkup(): void
    {
        $html = assessmentFieldsHtml(['InvaliditaTotaleAdulti' => "1' onfocus='alert(1)"], defaultRules());

        self::assertStringNotContainsString('alert', $html);
        self::assertStringContainsString("name='InvaliditaTotaleAdulti' min='0' max='22' value='0'", $html);
    }
}

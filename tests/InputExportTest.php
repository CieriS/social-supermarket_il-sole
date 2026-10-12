<?php

declare(strict_types=1);

namespace IlSole\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Export\exportCell;
use function IlSole\Export\exportLine;
use function IlSole\Html\escapeRow;
use function IlSole\Html\options;
use function IlSole\Input\cardCode;
use function IlSole\Input\email;
use function IlSole\Input\singleLine;
use function IlSole\Input\taxCode;
use function IlSole\Input\text;
use function IlSole\Input\wholeNumber;

final class InputExportTest extends TestCase
{
    #[DataProvider('taxCodes')]
    public function testTaxCode(mixed $value, string $expected): void
    {
        self::assertSame($expected, taxCode($value));
    }

    public static function taxCodes(): array
    {
        return [
            'upper case' => ['RSSMRA80A01A944X', 'RSSMRA80A01A944X'],
            'lower case and spaces' => [' rssmra80a01a944x ', 'RSSMRA80A01A944X'],
            'too short' => ['RSSMRA80A01A944', ''],
            'too long' => ['RSSMRA80A01A944XX', ''],
            'wildcard' => ['%', ''],
            'sixteen wildcards' => ['%%%%%%%%%%%%%%%%', ''],
            'SQL' => ["x' OR '1'='1", ''],
            'valid code followed by SQL' => ["RSSMRA80A01A944X' OR '1'='1", ''],
            'empty' => ['', ''],
            'missing' => [null, ''],
            'list' => [['RSSMRA80A01A944X'], ''],
        ];
    }

    #[DataProvider('cardCodes')]
    public function testCardCode(mixed $value, string $expected): void
    {
        self::assertSame($expected, cardCode($value));
    }

    public static function cardCodes(): array
    {
        return [
            ['abc123', 'ABC123'], ['A', 'A'], ['ABCDEFGHIJKL', 'ABCDEFGHIJKL'], ['ABCDEFGHIJKLM', ''],
            ['', ''], ['A-1', ''], ["A'); DROP TABLE Tessera;--", ''], ['<b>', ''], [null, ''], [['A'], ''],
        ];
    }

    #[DataProvider('emails')]
    public function testEmail(mixed $value, string $expected): void
    {
        self::assertSame($expected, email($value));
    }

    public static function emails(): array
    {
        return [
            ['mario.rossi@example.com', 'mario.rossi@example.com'],
            [' mario@example.com ', 'mario@example.com'],
            ['mario', ''], ['mario@', ''], ["mario@example.com\r\nBcc: x@example.com", ''], ['', ''], [null, ''],
        ];
    }

    public function testSingleLineKeepsHeadersFromBeingAdded(): void
    {
        self::assertSame('Avviso Bcc: x@example.com', singleLine("Avviso\r\nBcc: x@example.com"));
        self::assertSame('Avviso', singleLine("  Avviso\n"));
        self::assertSame('', singleLine(null));
    }

    public function testTextTrimsAndRefusesLists(): void
    {
        self::assertSame('abc', text('  abc '));
        self::assertSame('12', text(12));
        self::assertSame('', text(['abc']));
        self::assertSame('', text(null));
    }

    #[DataProvider('wholeNumbers')]
    public function testWholeNumber(mixed $value, ?int $expected): void
    {
        self::assertSame($expected, wholeNumber($value, 0, 120));
    }

    public static function wholeNumbers(): array
    {
        return [
            ['0', 0], ['120', 120], [' 6 ', 6], [6, 6], ['121', null], ['-1', null], ['6.5', null],
            ['', null], ['sei', null], [null, null], [['6'], null], ['99999999999', null],
        ];
    }

    public function testEscapeRowEscapesTextAndKeepsTheRest(): void
    {
        self::assertSame(
            ['Cognome' => '&lt;i&gt;D&#039;Angelo&lt;/i&gt;', 'nMembri' => 3, 'Scadenza' => null],
            escapeRow(['Cognome' => "<i>D'Angelo</i>", 'nMembri' => 3, 'Scadenza' => null])
        );
        self::assertNull(escapeRow(null));
        self::assertSame([], escapeRow([]));
    }

    public function testOptions(): void
    {
        self::assertSame(
            "<option value=''></option><option value='A' selected>A</option><option value='B&amp;C'>B e C</option>",
            options(['A', 'B&C'], 'A', ['B&C' => 'B e C'])
        );
        self::assertSame("<option value=''></option>", options([], 'A'));
    }

    #[DataProvider('cells')]
    public function testExportCell(mixed $value, string $expected): void
    {
        self::assertSame($expected, exportCell($value));
    }

    public static function cells(): array
    {
        return [
            'text' => ['Rossi', 'Rossi'],
            'nothing' => [null, ''],
            'number' => [102, '102'],
            'negative number stays a number' => ['-22', '-22'],
            'decimal' => ['450.50', '450.50'],
            'tab' => ["a\tb", 'a\\tb'],
            'new lines' => ["a\r\nb\nc\rd", 'a\\nb\\nc\\nd'],
            'quotes' => ['detto "Gianni"', '"detto ""Gianni"""'],
            'formula' => ['=HYPERLINK("http://example.com")', '"\'=HYPERLINK(""http://example.com"")"'],
            'plus' => ['+39 333', "'+39 333"],
            'minus text' => ['-cmd', "'-cmd"],
            'at' => ['@SUM(A1)', "'@SUM(A1)"],
            'list' => [['x'], ''],
        ];
    }

    public function testExportLine(): void
    {
        self::assertSame("Rossi\t\t-22\t'=1+1\n", exportLine(['Cognome' => 'Rossi', 'Tessera' => null, 'Benefit' => -22, 'Note' => '=1+1']));
    }
}

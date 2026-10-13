<?php

declare(strict_types=1);

namespace IlSole\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Config\isOn;
use function IlSole\Config\resolve;
use function IlSole\Demo\resetIsDue;
use function IlSole\Demo\withoutTransactionControl;

use const IlSole\Demo\FILES;
use const IlSole\Demo\TABLES;

final class ConfigDemoTest extends TestCase
{
    #[DataProvider('settings')]
    public function testResolve(array $environment, array $file, string $expected): void
    {
        self::assertSame($expected, resolve($environment, $file, 'DB_HOST', 'localhost'));
    }

    public static function settings(): array
    {
        return [
            'nowhere: the default' => [[], [], 'localhost'],
            'only in the file' => [[], ['DB_HOST' => 'sql.example.com'], 'sql.example.com'],
            'only in the environment' => [['DB_HOST' => 'db'], [], 'db'],
            'the environment wins' => [['DB_HOST' => 'db'], ['DB_HOST' => 'sql.example.com'], 'db'],
            'empty in the environment: the file' => [['DB_HOST' => ''], ['DB_HOST' => 'sql.example.com'], 'sql.example.com'],
            'empty everywhere: the default' => [['DB_HOST' => ''], ['DB_HOST' => ''], 'localhost'],
            'a number in the file' => [[], ['DB_HOST' => 3306], '3306'],
            'not a plain value in the file' => [[], ['DB_HOST' => ['x']], 'localhost'],
            'another setting does not count' => [['DB_USER' => 'root'], ['DB_NAME' => 'x'], 'localhost'],
        ];
    }

    public function testResolveWithoutADefaultIsEmpty(): void
    {
        self::assertSame('', resolve([], [], 'DB_PASSWORD'));
    }

    #[DataProvider('switches')]
    public function testIsOn(string $value, bool $expected): void
    {
        self::assertSame($expected, isOn($value));
    }

    public static function switches(): array
    {
        return [
            ['1', true], ['true', true], ['TRUE', true], [' on ', true], ['yes', true],
            ['', false], ['0', false], ['false', false], ['off', false], ['no', false], ['2', false],
        ];
    }

    #[DataProvider('resets')]
    public function testResetIsDue(?int $secondsAgo, bool $expected): void
    {
        $now = 1_800_000_000;

        self::assertSame($expected, resetIsDue($secondsAgo === null ? null : $now - $secondsAgo, $now, 3600));
    }

    public static function resets(): array
    {
        return [
            'never reset' => [null, true],
            'just reset' => [0, false],
            'a second before the interval' => [3599, false],
            'at the interval' => [3600, true],
            'long ago' => [86400, true],
            'a time in the future is not trusted' => [-60, true],
        ];
    }

    public function testAResetEmptiesEveryTableTheFilesFill(): void
    {
        $filled = [];
        foreach (FILES as $file) {
            preg_match_all('/INSERT INTO `(\w+)`/', file_get_contents(__DIR__ . "/../db/$file"), $matches);
            $filled = array_merge($filled, $matches[1]);
        }

        self::assertSame([], array_values(array_diff(array_unique($filled), TABLES)));
    }

    public function testAResetEmptiesChildrenBeforeTheApplicants(): void
    {
        $applicants = array_search('Richiedente', TABLES, true);

        foreach (['Membri', 'Punteggio', 'TesseraRilasciata', 'StoricoTessere', 'ValutazioneGriglia'] as $child) {
            self::assertLessThan($applicants, array_search($child, TABLES, true), $child);
        }
    }

    public function testTheFilesOfAResetCannotEndItsTransaction(): void
    {
        $sql = "SET NAMES utf8mb4;\nSTART TRANSACTION;\n\nINSERT INTO `Stallo` (`nMesiStallo`) VALUES\n(12);\n  commit ;\nBEGIN;\n";

        $clean = withoutTransactionControl($sql);

        self::assertStringNotContainsStringIgnoringCase('START TRANSACTION', $clean);
        self::assertStringNotContainsStringIgnoringCase('commit', $clean);
        self::assertStringNotContainsStringIgnoringCase('begin', $clean);
        self::assertStringContainsString("SET NAMES utf8mb4;", $clean);
        self::assertStringContainsString("INSERT INTO `Stallo` (`nMesiStallo`) VALUES\n(12);", $clean);
    }

    public function testTheDemoFilesAreLeftWithTheirRowsOnly(): void
    {
        foreach (FILES as $file) {
            $clean = withoutTransactionControl(file_get_contents(__DIR__ . "/../db/$file"));

            self::assertDoesNotMatchRegularExpression('/^\s*(START TRANSACTION|COMMIT)\b/mi', $clean, $file);
            self::assertStringContainsString('INSERT INTO', $clean, $file);
        }
    }
}

<?php

declare(strict_types=1);

namespace IlSole\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Auth\csrfMatches;
use function IlSole\Auth\sessionIsValid;

use const IlSole\Auth\SESSION_TTL;

final class AuthTest extends TestCase
{
    private const NOW = 1_800_000_000;

    #[DataProvider('sessions')]
    public function testSessionIsValid(array $session, bool $expected): void
    {
        self::assertSame($expected, sessionIsValid($session, self::NOW));
    }

    public static function sessions(): array
    {
        return [
            'just logged in' => [['operator' => 'gianni', 'lastSeen' => self::NOW], true],
            'at the limit' => [['operator' => 'gianni', 'lastSeen' => self::NOW - SESSION_TTL], true],
            'one second too late' => [['operator' => 'gianni', 'lastSeen' => self::NOW - SESSION_TTL - 1], false],
            'empty session' => [[], false],
            'no operator' => [['lastSeen' => self::NOW], false],
            'empty operator' => [['operator' => '', 'lastSeen' => self::NOW], false],
            'operator of the wrong type' => [['operator' => true, 'lastSeen' => self::NOW], false],
            'never seen' => [['operator' => 'gianni'], false],
            'last seen as text' => [['operator' => 'gianni', 'lastSeen' => (string) self::NOW], false],
            'last seen in the future' => [['operator' => 'gianni', 'lastSeen' => self::NOW + 60], false],
        ];
    }

    #[DataProvider('tokens')]
    public function testCsrfMatches(array $session, mixed $token, bool $expected): void
    {
        self::assertSame($expected, csrfMatches($session, $token));
    }

    public static function tokens(): array
    {
        return [
            'same token' => [['csrf' => 'abc123'], 'abc123', true],
            'different token' => [['csrf' => 'abc123'], 'abc124', false],
            'no token sent' => [['csrf' => 'abc123'], null, false],
            'empty token sent' => [['csrf' => 'abc123'], '', false],
            'token sent as a list' => [['csrf' => 'abc123'], ['abc123'], false],
            'session without a token' => [[], 'abc123', false],
            'both empty' => [['csrf' => ''], '', false],
        ];
    }
}

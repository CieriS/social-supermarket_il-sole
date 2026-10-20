<?php

declare(strict_types=1);

namespace IlSole\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every script, stylesheet, font and image the pages load is served by the
 * application itself. A file from another host (a CDN) sends the visitor's
 * address to a third party, which the privacy policy of the site would then
 * have to declare. Libraries live in FileInclude/third-party/ instead.
 */
final class ThirdPartyTest extends TestCase
{
    /** What scripts/package.sh uploads, minus the libraries copied on purpose. */
    private const SERVED = ['index.php', 'home.html', 'FileToBeExecuted', 'FileInclude', 'src'];
    private const LIBRARIES = 'FileInclude/third-party/';

    public function testNoPageLoadsAFileFromAnotherHost(): void
    {
        $found = [];
        foreach (self::servedFiles() as $path) {
            foreach (self::externalResources((string) file_get_contents(self::root() . $path)) as $url) {
                $found[] = "$path: $url";
            }
        }

        self::assertSame([], $found);
    }

    public function testTheLibrariesAreThere(): void
    {
        $libraries = self::root() . self::LIBRARIES;
        $referenced = [];
        foreach (self::servedFiles() as $path) {
            $markup = (string) file_get_contents(self::root() . $path);
            preg_match_all('#third-party/([^"\'\s>]+)#', $markup, $matches);
            array_push($referenced, ...$matches[1]);
        }

        self::assertNotEmpty($referenced);
        foreach (array_unique($referenced) as $file) {
            self::assertFileExists($libraries . $file);
        }
        foreach (glob($libraries . '*', GLOB_ONLYDIR) as $library) {
            self::assertFileExists("$library/LICENSE.txt");
        }
    }

    #[DataProvider('markup')]
    public function testExternalResources(string $markup, array $expected): void
    {
        self::assertSame($expected, self::externalResources($markup));
    }

    public static function markup(): array
    {
        return [
            'script from a CDN' => ['<script src="https://ajax.googleapis.com/jquery.min.js"></script>', ['https://ajax.googleapis.com/jquery.min.js']],
            'stylesheet, attribute after rel' => ["<link rel='stylesheet' href='https://cdnjs.cloudflare.com/a.css'>", ['https://cdnjs.cloudflare.com/a.css']],
            'protocol-relative' => ['<img Class="Logo" src="//cdn.example.com/a.png" />', ['//cdn.example.com/a.png']],
            'plain http, upper-case tag' => ['<SCRIPT SRC="http://example.com/a.js"></SCRIPT>', ['http://example.com/a.js']],
            'frame' => ['<iframe src="https://www.youtube.com/embed/x"></iframe>', ['https://www.youtube.com/embed/x']],
            'CSS url()' => ['body { background: url("https://example.com/sun.jpg"); }', ['https://example.com/sun.jpg']],
            'CSS @import' => ["@import 'https://fonts.googleapis.com/css?family=Roboto';", ['https://fonts.googleapis.com/css?family=Roboto']],
            'local script' => ['<script src="../FileInclude/third-party/jquery-3.7.1/jquery.min.js"></script>', []],
            'local CSS url()' => ['.a { background: url(../immagini/Footer.png); }', []],
            'data URI' => ['.a { background: url("data:image/gif;base64,R0lG"); }', []],
            'a link is not a resource' => ["<a href='https://cierisamuele.vercel.app' target=\"_blank\">", []],
            'an absolute path on the same host' => ['<script src="/il-sole/a.js"></script>', []],
        ];
    }

    /** @return list<string> the URLs on another host that the markup or stylesheet loads */
    private static function externalResources(string $markup): array
    {
        $external = '(?:https?:)?//[^"\'\s)>]+';
        preg_match_all(
            '#<(?:script|link|img|iframe|source|audio|video|embed)\b[^>]*?\b(?:src|href)\s*=\s*["\']?(' . $external . ')'
            . '|url\(\s*["\']?(' . $external . ')'
            . '|@import\s+["\'](' . $external . ')#i',
            $markup,
            $matches,
            PREG_SET_ORDER,
        );

        return array_map(static fn (array $match): string => (string) end($match), $matches);
    }

    /** @return list<string> paths relative to the repository root */
    private static function servedFiles(): array
    {
        $files = [];
        foreach (self::SERVED as $entry) {
            if (is_file(self::root() . $entry)) {
                $files[] = $entry;
                continue;
            }
            $tree = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::root() . $entry, \FilesystemIterator::SKIP_DOTS));
            foreach ($tree as $file) {
                $path = substr($file->getPathname(), strlen(self::root()));
                if (preg_match('/\.(?:php|html|css|js)$/', $path) && !str_starts_with($path, self::LIBRARIES)) {
                    $files[] = $path;
                }
            }
        }
        sort($files);

        return $files;
    }

    private static function root(): string
    {
        return dirname(__DIR__) . '/';
    }
}

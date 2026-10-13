<?php

/*
    Settings of the installation. They come from the environment (Docker) or,
    where there is none to set (shared hosting), from a config.php file next to
    index.php that returns an array; see config.example.php.
*/

declare(strict_types=1);

namespace IlSole\Config;

/* Pure: the environment wins over the file, an empty value counts as missing */
function resolve(array $environment, array $file, string $name, string $default = ''): string
{
    foreach ([$environment, $file] as $source) {
        $value = $source[$name] ?? null;
        if (is_scalar($value) && (string) $value !== '') {
            return (string) $value;
        }
    }
    return $default;
}

function isOn(string $value): bool
{
    return in_array(strtolower(trim($value)), ['1', 'true', 'on', 'yes'], true);
}

function setting(string $name, string $default = ''): string
{
    static $file = null;
    if ($file === null) {
        $path = __DIR__ . '/../config.php';
        $loaded = is_file($path) ? require $path : [];
        $file = is_array($loaded) ? $loaded : [];
    }
    $value = getenv($name);
    return resolve($value === false ? [] : [$name => $value], $file, $name, $default);
}

/* The public demonstration: no real mail, fixed credentials, data restored now and then */
function demoMode(): bool
{
    return isOn(setting('DEMO_MODE'));
}

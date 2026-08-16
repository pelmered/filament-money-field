<?php

declare(strict_types=1);

/**
 * Drives the Filament 4 / Laravel 11 test environment, which lives in vendor-f4/
 * alongside the default Filament 5 / Laravel 13 one in vendor/.
 *
 * composer-f4.json and phpunit-f4.xml are generated from composer.json and
 * phpunit.xml so the two environments cannot drift apart. Pointing composer at
 * composer-f4.json leaves the default vendor/ and composer.lock untouched, so
 * both environments stay installed and can be tested and served side by side.
 *
 * Laravel 11 does not run on PHP 8.5, so every command is dispatched to a PHP
 * 8.4 binary. Set PHP_F4 to choose one explicitly.
 */
const VENDOR_DIR = 'vendor-f4';

const COMPOSER_FILE = 'composer-f4.json';

const PHPUNIT_FILE = 'phpunit-f4.xml';

const CACHE_DIR = '.phpunit-f4.cache';

const PORT = '8004';

const SKELETON_AUTOLOAD = VENDOR_DIR.'/orchestra/testbench-core/laravel/bootstrap/autoload.php';

$root = dirname(__DIR__);

$command = $argv[1] ?? 'install';
$extra   = array_slice($argv, 2);

// Keeps nested composer and testbench calls on the F4 config rather than the
// default one they would otherwise pick up from the working directory.
putenv('COMPOSER='.COMPOSER_FILE);

$php = resolvePhpBinary();

exit(match ($command) {
    'install' => install($root, $php),
    // Pest directly, not "testbench package:test": that resolves the runner as
    // "vendor/pestphp/pest/bin/pest", and the F5 autoloader it boots with would
    // win over the F4 one this config bootstraps.
    'test'  => run($php, [VENDOR_DIR.'/bin/pest', '-c', PHPUNIT_FILE, ...$extra]),
    'serve' => serve($php, $extra),
    default => fail('Unknown command "'.$command.'". Expected install, test or serve.'),
});

/** @param list<string> $extra */
function serve(string $php, array $extra): int
{
    pointSkeletonAtVendorDir(dirname(__DIR__));

    $built = run($php, [VENDOR_DIR.'/bin/testbench', 'workbench:build', '--ansi']);

    if ($built !== 0) {
        return $built;
    }

    return run($php, [VENDOR_DIR.'/bin/testbench', 'serve', '--port='.PORT, ...$extra]);
}

function install(string $root, string $php): int
{
    $encode = static fn (mixed $value): string => json_encode(
        $value,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );

    $composer = json_decode(file_get_contents($root.'/composer.json'), true, 512, JSON_THROW_ON_ERROR);

    // Both are subsets of what composer.json already allows, so this pins the
    // environment without loosening the package's real constraints. Testbench 9
    // is the Laravel 11 line; 10 is Laravel 12 and 11 is Laravel 13.
    $composer['require-dev']['filament/filament']   = '^4.0';
    $composer['require-dev']['orchestra/testbench'] = '^9.0';

    $composer['config']['vendor-dir'] = VENDOR_DIR;

    // Rewritten rather than dropped: the install below fires post-autoload-dump,
    // which purges the skeleton and re-runs package discovery through these.
    $composer['scripts'] = json_decode(
        str_replace('vendor/bin/', VENDOR_DIR.'/bin/', $encode($composer['scripts'])),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    file_put_contents($root.'/'.COMPOSER_FILE, $encode($composer)."\n");

    // A second autoloader next to vendor/'s would resolve Filament out of
    // whichever registered first, so the F4 suite needs its own bootstrap.
    file_put_contents($root.'/'.PHPUNIT_FILE, str_replace(
        ['vendor/autoload.php', '.phpunit.cache'],
        [VENDOR_DIR.'/autoload.php', CACHE_DIR],
        file_get_contents($root.'/phpunit.xml')
    ));

    // COMPOSER_BINARY is set by composer whenever it runs a script.
    $exitCode = run($php, [$_SERVER['COMPOSER_BINARY'] ?? 'composer', 'update', '--no-interaction']);

    if ($exitCode === 0) {
        pointSkeletonAtVendorDir($root);
    }

    return $exitCode;
}

/**
 * The served skeleton bootstraps itself from "<package>/vendor/autoload.php":
 * testbench assumes a package's vendor directory is always named "vendor", so
 * without this the F4 server boots the Filament 5 environment instead. Composer
 * restores the file whenever testbench-core is reinstalled, so this is reapplied
 * on both install and serve, and fails loudly rather than quietly serving F5.
 */
function pointSkeletonAtVendorDir(string $root): void
{
    $path     = $root.'/'.SKELETON_AUTOLOAD;
    $expected = "'/".VENDOR_DIR."/autoload.php'";

    $contents = @file_get_contents($path);

    if ($contents === false) {
        fail('Missing '.SKELETON_AUTOLOAD.'. Run "composer env:f4" first.');
    }

    $patched = str_replace("'/vendor/autoload.php'", $expected, $contents);

    if (! str_contains($patched, $expected)) {
        fail(
            'Could not point '.SKELETON_AUTOLOAD.' at '.VENDOR_DIR.'/. Testbench changed its '
            .'skeleton bootstrap, and the F4 server would silently serve Filament 5.'
        );
    }

    if ($patched !== $contents) {
        file_put_contents($path, $patched);
    }
}

function resolvePhpBinary(): string
{
    if (($explicit = getenv('PHP_F4')) !== false && $explicit !== '') {
        return $explicit;
    }

    if (PHP_VERSION_ID < 80500) {
        return PHP_BINARY;
    }

    foreach (['php8.4', 'php84', 'php8.3', 'php83'] as $candidate) {
        $path = trim((string) shell_exec('command -v '.escapeshellarg($candidate).' 2>/dev/null'));

        if ($path !== '') {
            return $path;
        }
    }

    fail(
        'Laravel 11 does not support PHP '.PHP_VERSION.'. Install PHP 8.4 (Herd ships it as "php84") '
        .'or point PHP_F4 at a suitable binary.'
    );
}

/** @param list<string> $arguments */
function run(string $php, array $arguments): int
{
    passthru(implode(' ', array_map(escapeshellarg(...), [$php, ...$arguments])), $exitCode);

    return $exitCode;
}

function fail(string $message): never
{
    fwrite(STDERR, $message.PHP_EOL);

    exit(1);
}

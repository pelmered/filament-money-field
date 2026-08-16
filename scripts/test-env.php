<?php

declare(strict_types=1);

/**
 * Drives the alternate test environments that live alongside the default one in
 * vendor/, which is the "latest" environment and needs no driver.
 *
 * Each environment gets its own vendor directory, driven by a generated
 * composer-<name>.json and phpunit-<name>.xml derived from composer.json and
 * phpunit.xml, so an environment cannot drift from the package's real
 * constraints. Pointing composer at the generated file leaves the default
 * vendor/ and composer.lock untouched, so every environment stays installed and
 * they can be tested and served side by side.
 *
 * Usage: php scripts/test-env.php <environment> <install|test|serve> [arguments...]
 */

/**
 * Named for what each environment is for, matching the browser job names in
 * .github/workflows/tests.yml. Testbench majors track Laravel: 9 is Laravel 11,
 * 10 is Laravel 12, 11 is Laravel 13. Both constraints are subsets of what
 * composer.json already allows, so an environment pins a combination without
 * loosening the package's own support.
 *
 * "php" is the version the environment targets: dependencies always resolve for
 * it, so this matches CI even when the installed binary is a different version.
 * "max_php" is the highest version the environment's Laravel runs on.
 */
const TEST_ENVIRONMENTS = [
    // Lowest supported combination. PHP 8.3 rather than the package floor of
    // 8.2, because pest-plugin-browser requires ^8.3 and could not run the
    // Browser suite any lower.
    'lowest' => [
        'filament'  => '^4.0',
        'testbench' => '^9.0',
        'php'       => '8.3',
        'max_php'   => '8.4',
        'port'      => '8004',
    ],
    // Latest Filament on the previous Laravel: the combination neither end of
    // the support range covers, and a common one in the wild.
    'middle' => [
        'filament'  => '^5.0',
        'testbench' => '^10.0',
        'php'       => '8.4',
        'max_php'   => '8.4',
        'port'      => '8006',
    ],
];

$root = dirname(__DIR__);

$name    = $argv[1] ?? '';
$command = $argv[2] ?? 'install';
$extra   = array_slice($argv, 3);

if (! isset(TEST_ENVIRONMENTS[$name])) {
    fail(
        'Unknown environment "'.$name.'". Expected one of: '.implode(', ', array_keys(TEST_ENVIRONMENTS))
        .'. The "latest" environment is the default vendor/ and is driven by composer directly.'
    );
}

$environment = TEST_ENVIRONMENTS[$name] + [
    'name'     => $name,
    'vendor'   => 'vendor-'.$name,
    'composer' => 'composer-'.$name.'.json',
    'phpunit'  => 'phpunit-'.$name.'.xml',
    'cache'    => '.phpunit-'.$name.'.cache',
];

// Keeps nested composer and testbench calls on this environment's config rather
// than the default one they would otherwise pick up from the working directory.
putenv('COMPOSER='.$environment['composer']);

$php = resolvePhpBinary($environment);

announce($environment, $php, $command);

exit(match ($command) {
    'install' => install($environment, $root, $php),
    // Pest directly, not "testbench package:test": that resolves the runner as
    // "vendor/pestphp/pest/bin/pest", and the default autoloader it boots with
    // would win over the one this environment's config bootstraps.
    'test'  => run($php, [$environment['vendor'].'/bin/pest', '-c', $environment['phpunit'], ...$extra]),
    'serve' => serve($environment, $root, $php, $extra),
    default => fail('Unknown command "'.$command.'". Expected install, test or serve.'),
});

/**
 * @param  array<string, string>  $environment
 * @param  list<string>  $extra
 */
function serve(array $environment, string $root, string $php, array $extra): int
{
    pointSkeletonAtVendorDir($environment, $root);

    $built = run($php, [$environment['vendor'].'/bin/testbench', 'workbench:build', '--ansi']);

    if ($built !== 0) {
        return $built;
    }

    return run($php, [
        $environment['vendor'].'/bin/testbench', 'serve', '--port='.$environment['port'], ...$extra,
    ]);
}

/** @param  array<string, string>  $environment */
function install(array $environment, string $root, string $php): int
{
    $encode = static fn (mixed $value): string => json_encode(
        $value,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );

    $composer = json_decode(file_get_contents($root.'/composer.json'), true, 512, JSON_THROW_ON_ERROR);

    $composer['require-dev']['filament/filament']   = $environment['filament'];
    $composer['require-dev']['orchestra/testbench'] = $environment['testbench'];

    $composer['config']['vendor-dir'] = $environment['vendor'];

    // Resolve for the target PHP rather than whatever happens to be installed,
    // so this matches CI. Symfony 8 needs 8.4, for example, so an unpinned 8.4
    // install would quietly test a newer set than an 8.3 environment does.
    $composer['config']['platform']['php'] = $environment['php'];

    // Rewritten rather than dropped: the install below fires post-autoload-dump,
    // which purges the skeleton and re-runs package discovery through these.
    $composer['scripts'] = json_decode(
        str_replace('vendor/bin/', $environment['vendor'].'/bin/', $encode($composer['scripts'])),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    file_put_contents($root.'/'.$environment['composer'], $encode($composer)."\n");

    // A second autoloader next to vendor/'s would resolve Filament out of
    // whichever registered first, so each environment needs its own bootstrap.
    file_put_contents($root.'/'.$environment['phpunit'], str_replace(
        ['vendor/autoload.php', '.phpunit.cache'],
        [$environment['vendor'].'/autoload.php', $environment['cache']],
        file_get_contents($root.'/phpunit.xml')
    ));

    // COMPOSER_BINARY is set by composer whenever it runs a script.
    $exitCode = run($php, [$_SERVER['COMPOSER_BINARY'] ?? 'composer', 'update', '--no-interaction']);

    if ($exitCode === 0) {
        pointSkeletonAtVendorDir($environment, $root);
    }

    return $exitCode;
}

/**
 * The served skeleton bootstraps itself from "<package>/vendor/autoload.php":
 * testbench assumes a package's vendor directory is always named "vendor", so
 * without this an environment's server boots the default one instead. Composer
 * restores the file whenever testbench-core is reinstalled, so this is reapplied
 * on both install and serve, and fails loudly rather than serving the wrong one.
 *
 * @param  array<string, string>  $environment
 */
function pointSkeletonAtVendorDir(array $environment, string $root): void
{
    $relative = $environment['vendor'].'/orchestra/testbench-core/laravel/bootstrap/autoload.php';
    $expected = "'/".$environment['vendor']."/autoload.php'";

    $contents = @file_get_contents($root.'/'.$relative);

    if ($contents === false) {
        fail('Missing '.$relative.'. Run "composer env:'.$environment['name'].'" first.');
    }

    $patched = str_replace("'/vendor/autoload.php'", $expected, $contents);

    if (! str_contains($patched, $expected)) {
        fail(
            'Could not point '.$relative.' at '.$environment['vendor'].'/. Testbench changed its skeleton '
            .'bootstrap, and this environment would silently serve the default one.'
        );
    }

    if ($patched !== $contents) {
        file_put_contents($root.'/'.$relative, $patched);
    }
}

/** @param  array<string, string>  $environment */
function resolvePhpBinary(array $environment): string
{
    $override = getenv('PHP_'.strtoupper($environment['name']));

    if ($override !== false && $override !== '') {
        return $override;
    }

    // Target version first, then upwards to the ceiling. Never downwards:
    // dependencies resolve for the target, so an older runtime could be handed a
    // package it cannot run.
    foreach (phpBinaryNames($environment['php'], $environment['max_php']) as $candidate) {
        $path = trim((string) shell_exec('command -v '.escapeshellarg($candidate).' 2>/dev/null'));

        if ($path !== '') {
            return $path;
        }
    }

    if (
        version_compare(PHP_VERSION, $environment['php'], '>=')
        && version_compare(PHP_VERSION, $environment['max_php'].'.999', '<=')
    ) {
        return PHP_BINARY;
    }

    fail(
        'Environment "'.$environment['name'].'" needs PHP '.$environment['php'].' to '
        .$environment['max_php'].', but this is PHP '.PHP_VERSION.'. Install it (Herd: "herd install php@'
        .$environment['php'].'") or set PHP_'.strtoupper($environment['name']).' to a suitable binary.'
    );
}

/** @return list<string> */
function phpBinaryNames(string $target, string $max): array
{
    [$major, $from] = array_map(intval(...), explode('.', $target));
    [, $to]         = array_map(intval(...), explode('.', $max));

    $names = [];

    for ($minor = $from; $minor <= $to; $minor++) {
        $names[] = 'php'.$major.'.'.$minor;
        $names[] = 'php'.$major.$minor;
    }

    return $names;
}

/**
 * The resolved PHP version decides what an environment actually proves, and it
 * is picked from whatever is installed — so state it rather than let it be guessed.
 *
 * @param  array<string, string>  $environment
 */
function announce(array $environment, string $php, string $command): void
{
    $version = trim((string) shell_exec(escapeshellarg($php).' -r "echo PHP_VERSION;" 2>/dev/null'));
    $running = $version !== '' ? $version : 'unknown';

    // Dependencies always resolve for the target; only the runtime can differ.
    $drift = str_starts_with($running, $environment['php'].'.') ? '' : ' (resolved for '.$environment['php'].')';

    fwrite(STDERR, '→ '.$environment['name'].' '.$command.' · '.$environment['vendor'].'/ · PHP '.$running.$drift.PHP_EOL);
}

/** @param  list<string>  $arguments */
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

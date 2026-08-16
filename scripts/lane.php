<?php

declare(strict_types=1);

/**
 * Drives the alternate test environments that live alongside the default one in
 * vendor/ (Filament 5 / Laravel 13 / PHP 8.5).
 *
 * Each lane gets its own vendor directory, driven by a generated
 * composer-<lane>.json and phpunit-<lane>.xml derived from composer.json and
 * phpunit.xml, so a lane cannot drift from the package's real constraints.
 * Pointing composer at the generated file leaves the default vendor/ and
 * composer.lock untouched, so every lane stays installed and they can be tested
 * and served side by side.
 *
 * Usage: php scripts/lane.php <lane> <install|test|serve> [arguments...]
 */

/**
 * Testbench majors track Laravel: 9 is Laravel 11, 10 is Laravel 12, 11 is
 * Laravel 13. Both constraints are subsets of what composer.json already allows,
 * so a lane pins an environment without loosening the package's own support.
 *
 * "php" is the version the lane targets: dependencies always resolve for it, so
 * a lane matches CI even when the installed binary is a different version.
 * "max_php" is the highest the lane's Laravel runs on.
 */
const LANES = [
    // Lowest supported stack. PHP 8.3 rather than the package floor of 8.2,
    // because pest-plugin-browser requires ^8.3 and could not run the Browser
    // suite any lower.
    'f4' => [
        'filament'  => '^4.0',
        'testbench' => '^9.0',
        'php'       => '8.3',
        'max_php'   => '8.4',
        'port'      => '8004',
    ],
    // Latest Filament on the previous Laravel: the combination neither end of
    // the support range covers, and a common one in the wild.
    'l12' => [
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

if (! isset(LANES[$name])) {
    fail('Unknown lane "'.$name.'". Expected one of: '.implode(', ', array_keys(LANES)).'.');
}

$lane = LANES[$name] + [
    'name'     => $name,
    'vendor'   => 'vendor-'.$name,
    'composer' => 'composer-'.$name.'.json',
    'phpunit'  => 'phpunit-'.$name.'.xml',
    'cache'    => '.phpunit-'.$name.'.cache',
];

// Keeps nested composer and testbench calls on this lane's config rather than
// the default one they would otherwise pick up from the working directory.
putenv('COMPOSER='.$lane['composer']);

$php = resolvePhpBinary($lane);

announce($lane, $php, $command);

exit(match ($command) {
    'install' => install($lane, $root, $php),
    // Pest directly, not "testbench package:test": that resolves the runner as
    // "vendor/pestphp/pest/bin/pest", and the default autoloader it boots with
    // would win over the lane's own that this config bootstraps.
    'test'  => run($php, [$lane['vendor'].'/bin/pest', '-c', $lane['phpunit'], ...$extra]),
    'serve' => serve($lane, $root, $php, $extra),
    default => fail('Unknown command "'.$command.'". Expected install, test or serve.'),
});

/**
 * @param  array<string, string>  $lane
 * @param  list<string>  $extra
 */
function serve(array $lane, string $root, string $php, array $extra): int
{
    pointSkeletonAtVendorDir($lane, $root);

    $built = run($php, [$lane['vendor'].'/bin/testbench', 'workbench:build', '--ansi']);

    if ($built !== 0) {
        return $built;
    }

    return run($php, [$lane['vendor'].'/bin/testbench', 'serve', '--port='.$lane['port'], ...$extra]);
}

/** @param array<string, string> $lane */
function install(array $lane, string $root, string $php): int
{
    $encode = static fn (mixed $value): string => json_encode(
        $value,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );

    $composer = json_decode(file_get_contents($root.'/composer.json'), true, 512, JSON_THROW_ON_ERROR);

    $composer['require-dev']['filament/filament']   = $lane['filament'];
    $composer['require-dev']['orchestra/testbench'] = $lane['testbench'];

    $composer['config']['vendor-dir'] = $lane['vendor'];

    // Resolve for the lane's target PHP rather than whatever happens to be
    // installed, so this matches CI. Symfony 8 needs 8.4, for example, so an
    // unpinned 8.4 install would quietly test a newer set than an 8.3 lane does.
    $composer['config']['platform']['php'] = $lane['php'];

    // Rewritten rather than dropped: the install below fires post-autoload-dump,
    // which purges the skeleton and re-runs package discovery through these.
    $composer['scripts'] = json_decode(
        str_replace('vendor/bin/', $lane['vendor'].'/bin/', $encode($composer['scripts'])),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    file_put_contents($root.'/'.$lane['composer'], $encode($composer)."\n");

    // A second autoloader next to vendor/'s would resolve Filament out of
    // whichever registered first, so each lane needs its own bootstrap.
    file_put_contents($root.'/'.$lane['phpunit'], str_replace(
        ['vendor/autoload.php', '.phpunit.cache'],
        [$lane['vendor'].'/autoload.php', $lane['cache']],
        file_get_contents($root.'/phpunit.xml')
    ));

    // COMPOSER_BINARY is set by composer whenever it runs a script.
    $exitCode = run($php, [$_SERVER['COMPOSER_BINARY'] ?? 'composer', 'update', '--no-interaction']);

    if ($exitCode === 0) {
        pointSkeletonAtVendorDir($lane, $root);
    }

    return $exitCode;
}

/**
 * The served skeleton bootstraps itself from "<package>/vendor/autoload.php":
 * testbench assumes a package's vendor directory is always named "vendor", so
 * without this a lane's server boots the default environment instead. Composer
 * restores the file whenever testbench-core is reinstalled, so this is reapplied
 * on both install and serve, and fails loudly rather than serving the wrong one.
 *
 * @param  array<string, string>  $lane
 */
function pointSkeletonAtVendorDir(array $lane, string $root): void
{
    $relative = $lane['vendor'].'/orchestra/testbench-core/laravel/bootstrap/autoload.php';
    $expected = "'/".$lane['vendor']."/autoload.php'";

    $contents = @file_get_contents($root.'/'.$relative);

    if ($contents === false) {
        fail('Missing '.$relative.'. Run "composer env:'.$lane['name'].'" first.');
    }

    $patched = str_replace("'/vendor/autoload.php'", $expected, $contents);

    if (! str_contains($patched, $expected)) {
        fail(
            'Could not point '.$relative.' at '.$lane['vendor'].'/. Testbench changed its skeleton '
            .'bootstrap, and this lane would silently serve the default environment.'
        );
    }

    if ($patched !== $contents) {
        file_put_contents($root.'/'.$relative, $patched);
    }
}

/** @param array<string, string> $lane */
function resolvePhpBinary(array $lane): string
{
    $override = getenv('PHP_'.strtoupper($lane['name']));

    if ($override !== false && $override !== '') {
        return $override;
    }

    // Target version first, then upwards to the lane's ceiling. Never downwards:
    // dependencies resolve for the target, so an older runtime could be handed a
    // package it cannot run.
    foreach (phpBinaryNames($lane['php'], $lane['max_php']) as $candidate) {
        $path = trim((string) shell_exec('command -v '.escapeshellarg($candidate).' 2>/dev/null'));

        if ($path !== '') {
            return $path;
        }
    }

    if (version_compare(PHP_VERSION, $lane['php'], '>=') && version_compare(PHP_VERSION, $lane['max_php'].'.999', '<=')) {
        return PHP_BINARY;
    }

    fail(
        'Lane "'.$lane['name'].'" needs PHP '.$lane['php'].' to '.$lane['max_php'].', but this is PHP '
        .PHP_VERSION.'. Install it (Herd: "herd install php@'.$lane['php'].'") or set PHP_'
        .strtoupper($lane['name']).' to a suitable binary.'
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
 * The resolved PHP version decides what a lane actually proves, and it is picked
 * from whatever is installed — so state it rather than let it be guessed.
 *
 * @param  array<string, string>  $lane
 */
function announce(array $lane, string $php, string $command): void
{
    $version = trim((string) shell_exec(escapeshellarg($php).' -r "echo PHP_VERSION;" 2>/dev/null'));
    $running = $version !== '' ? $version : 'unknown';

    // Dependencies always resolve for the target; only the runtime can differ.
    $drift = str_starts_with($running, $lane['php'].'.') ? '' : ' (resolved for '.$lane['php'].')';

    fwrite(STDERR, '→ '.$lane['name'].' '.$command.' · '.$lane['vendor'].'/ · PHP '.$running.$drift.PHP_EOL);
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

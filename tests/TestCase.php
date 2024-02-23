<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Filament\Forms\FormsServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Pelmered\FilamentMoneyField\FilamentMoneyFieldServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            // Filament service providers
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            SupportServiceProvider::class,

            // This package service provider
            FilamentMoneyFieldServiceProvider::class,
        ];
    }

    public static function callMethod($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        return $class->getMethod($name)->invokeArgs($obj, $args);
    }

    public static function getProperty($object, $property)
    {
        $reflectedClass = new \ReflectionClass($object);
        $reflection = $reflectedClass->getProperty($property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }

    /**
     * Replaces all non-breaking spaces in the given string with the Unicode character for non-breaking space.
     */
    public static function replaceNonBreakingSpaces(string $string): string
    {
        return preg_replace('/\s/', "\xc2\xa0", $string);
    }
}

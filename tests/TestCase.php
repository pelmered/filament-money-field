<?php
namespace Pelmered\FilamentMoneyField\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
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

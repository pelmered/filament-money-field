<?php
namespace Pelmered\FilamentMoneyField\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    public static function callMethod($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        return $class->getMethod($name)->invokeArgs($obj, $args);
    }
    public static function replaceNonBreakingSpaces(string $string): string
    {
        return preg_replace('/\s/', "\xc2\xa0", $string);
    }
}

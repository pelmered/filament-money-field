<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Illuminate\Validation\ValidationException;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\TestCase;

pest()->project()->github('pelmered/filament-money-field');

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in('Unit');


/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Replaces all non-breaking spaces in the given string with the Unicode character for non-breaking space.
 */
function replaceNonBreakingSpaces(string $string): string
{
    return preg_replace('/\s/', "\xc2\xa0", $string);
}

function validationTester(Field $field, $value, ?callable $assertsCallback = null): true|array
{
    try {
        ComponentContainer::make(FormTestComponent::make())
            ->statePath('data')
            ->components([$field])
            ->fill([$field->getName() => $value])
            ->validate();
    } catch (ValidationException $exception) {
        if ($assertsCallback) {
            $assertsCallback($exception, $field);
        }

        return [
            'errors' => $exception->validator->errors()->toArray()[$field->getStatePath()],
            'failed' => $exception->validator->failed()[$field->getStatePath()],
        ];
    }

    return true;
}

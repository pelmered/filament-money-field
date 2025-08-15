<?php

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as F3Action;
use Filament\Forms\Components\Field;
use Filament\Infolists;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Helper;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;
use Pelmered\FilamentMoneyField\Tests\Support\Components\Filament3\FormTestComponent as F3FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\Support\Components\InfolistTestComponent;
use Pelmered\FilamentMoneyField\Tests\Support\Components\TableTestComponent;
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

uses(TestCase::class)->in('Unit', 'Components', 'Forms');

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

expect()->extend('toBeInstanceOfWithVersions', function ($class4, $class3): void {
    if(Helper::isFilament3())
    {
        $this->toBeInstanceOf($class3);
    }
    else
    {
        $this->toBeInstanceOf($class4);
    }
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
 * Replaces all non-breaking spaces in the given string with regular spaces.
 */
function replaceNonBreakingSpaces(string $string): string
{
    return str_replace(["\xC2\xA0", "\xE2\x80\xAF"], ' ', $string);
}

function validationTester(Field $field, $value, ?callable $assertsCallback = null): true|array
{
    try {
        $component = createFormTestComponent(
            [$field],
            [$field->getName() => $value]
        );

        $component->validate();
    } catch (ValidationException $validationException) {
        if ($assertsCallback !== null) {
            $assertsCallback($validationException, $field);
        }

        return [
            'errors' => $validationException->validator->errors()->toArray()[$field->getStatePath()],
            'failed' => $validationException->validator->failed()[$field->getStatePath()],
        ];
    }

    return true;
}

/**
 * @throws Exception
 */
function createTestComponent($type = 'form', array $components = [], ?string $fieldName = null)
{
    if (!$fieldName)
    {
        $fieldName = $components[0]->getName();
    }

    if (count($components) <= 0) {
        $components = match ($type) {
            'form'     => [MoneyInput::make($fieldName)],
            'infolist' => [MoneyEntry::make($fieldName)],
            'table'    => [MoneyColumn::make($fieldName)],
            default    => [],
        };
    }

    if (Helper::isFilament3())
    {
        return (match ($type) {
            'form'     => \Filament\Forms\ComponentContainer::make(F3FormTestComponent::make()),
            'infolist' => \Filament\Infolists\ComponentContainer::make(InfolistTestComponent::make()),
            //'table' =>  \Filament\Tables\ComponentContainer::make(TableTestComponent::make()),
            default => throw new Exception('Unknown component type: '.$type),
        })
            ->statePath('data')
            ->components($components);
    }

    return (match ($type) {
        'form'     => Schema::make(FormTestComponent::make()),
        'infolist' => Schema::make(InfolistTestComponent::make()),
        //'table' =>  Schema::make(TableTestComponent::make()),
        default => throw new Exception('Unknown component type: '.$type),
    })
        ->components($components);
}

function createFormTestComponent($components = [], $fill = [], ?string $fieldName = null)
{
    $components = createTestComponent('form', $components, $fieldName);
    $components->fill($fill);

    return $components;
}

function createInfolistTestComponent($components = [], $fill = [], ?string $fieldName = null)
{
    $components = createTestComponent('infolist', $components, $fieldName);
    //dd($components);
    $components->state($fill);

    return $components;
        //->getComponent($fieldName);
}

function getComponent($testComponent, string $componentName): MoneyInput|MoneyEntry
{
    return $testComponent->getComponent((Helper::isFilament3() ? 'data.' : '').$componentName);
}

<?php
namespace Pelmered\FilamentMoneyField\Tests;

use Filament\Forms\Components\Field;
use Mockery\MockInterface;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Filament\Forms\ComponentContainer;
use Illuminate\Support\Str;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule2;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tests\Components\FormTestComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Livewire\Livewire;
use Illuminate\Validation\ValidationException;

#[CoversClass(MoneyInput::class)]
class FormInputTest extends TestCase
{
    public function testFormInputMoneyFormat(): void
    {
        $component = ComponentContainer::make(FormTestComponent::make())
                          ->statePath('data')
                          ->components([
                              MoneyInput::make('price'),
                          ])->fill(['price' => 20]);

        $this->assertEquals('20', $component->getState()['price']);
    }

    public function validationTester(Field $field, $value): true|array
    {
        try {
            ComponentContainer::make(FormTestComponent::make())
                              ->statePath('data')
                              ->components([
                                  $field
                              ])->fill([$field->getName() => $value])
                              ->validate();
        } catch (ValidationException $exception) {
            return $exception->validator->failed()[$field->getStatePath()];
        }

        return true;
    }

    public function testMinAndMaxValues(): void
    {
        $this->assertTrue($this->validationTester(
            (new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000),
            20
        ));

        $this->assertArrayHasKey(MaxValueRule::class,$this->validationTester(
            (new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000),
            200
        ));

        $this->assertArrayHasKey(MinValueRule::class,$this->validationTester(
            (new MoneyInput('totalAmount'))->required()->minValue(10000)->maxValue(20000),
            20
        ));
    }
}

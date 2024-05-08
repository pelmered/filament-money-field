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
use Pelmered\FilamentMoneyField\Tests\Components\LivewireComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Livewire\Livewire;
use Illuminate\Validation\ValidationException;

#[CoversClass(MoneyInput::class)]
class FormInputTest extends TestCase
{

    /*
    public function testFormInputMoneyFormat(): void
    {
        $input = MoneyInput::make('price');
        //dd($input);
        //$this->assertEquals('$25.00', $input->formatState(2500));

        $this->assertEquals('$25.00', $input->formatState(2500));
    }
    */

    public function validationTester(Field $field, $value): true|array
    {
        $rules = [];
        //$fieldName = 'totalAmount';
        /*
        $field = (new MoneyInput($fieldName))
            ->required()->minValue(100)->maxValue(10000);
        */

        //$field->getName()

        try {
            ComponentContainer::make(LivewireComponent::make())
                              ->statePath('data')
                              ->components([
                                  $field
                              ])->fill([$field->getName() => $value])
                              ->validate();
        } catch (ValidationException $exception) {
            dump($exception->validator->failed()[$field->getStatePath()]);
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

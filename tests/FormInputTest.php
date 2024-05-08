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

    public function validationTester(Field $field, $value, ?callable $assertsCallback = null): true|array
    {
        try {
            ComponentContainer::make(FormTestComponent::make())
                              ->statePath('data')
                              ->components([
                                  $field
                              ])->fill([$field->getName() => $value])
                              ->validate();
        } catch (ValidationException $exception) {
            if ($assertsCallback) {
                $assertsCallback($exception, $field);
            }

            return [
                'errors' => $exception->validator->errors()->toArray()[$field->getStatePath()],
                'failed' => $exception->validator->failed()[$field->getStatePath()]
            ];
        }

        return true;
    }

    public function testMinAndMaxValues(): void
    {
        $this->assertTrue($this->validationTester(
            (new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000),
            20,
        ));

        $this->validationTester(
            (new MoneyInput('amount'))->required()->minValue(100)->maxValue(10000),
            20,
            function (ValidationException $exception, MoneyInput $field) {
                $this->assertArrayHasKey(MinValueRule::class, $exception->validator->failed()[$field->getStatePath()]);
                $this->assertEquals(
                    'The Amount must be less than or equal to 100.00.',
                    $exception->validator->errors()->toArray()[$field->getStatePath()][0]
                );
            }
        );

        $this->validationTester(
            (new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000),
            200,
            function (ValidationException $exception, MoneyInput $field) {
                $this->assertArrayHasKey(MaxValueRule::class, $exception->validator->failed()[$field->getStatePath()]);
                $this->assertEquals(
                    'The Total Amount must be less than or equal to 100.00.',
                    $exception->validator->errors()->toArray()[$field->getStatePath()][0]
                );
            }
        );



        $this->validationTester(
            (new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000),
            'random string',
            function (ValidationException $exception, MoneyInput $field) {
                $failed = $exception->validator->failed()[$field->getStatePath()];

                $this->assertArrayHasKey(MinValueRule::class, $failed);
                $this->assertArrayHasKey(MaxValueRule::class, $failed);
                $this->assertEquals(
                    'The Total Amount must be a valid numeric value.',
                    $exception->validator->errors()->toArray()[$field->getStatePath()][0]
                );
            }
        );
    }
}

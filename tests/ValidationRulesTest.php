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
class ValidationRulesTest extends TestCase
{
    public function testMinValueRule()
    {
        $rule = new MinValueRule(10000, new MoneyInput('totalAmount'));

        $rule->validate('totalAmount', 16, function ($message) {
            $this->assertEquals('The Total Amount must be at least 100.00.', $message);
        });
    }
}

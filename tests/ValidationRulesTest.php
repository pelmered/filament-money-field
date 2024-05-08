<?php
namespace Pelmered\FilamentMoneyField\Tests;

use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;

class ValidationRulesTest extends TestCase
{
    public function testMinValueRule(): void
    {
        $rule = new MinValueRule(10000, new MoneyInput('totalAmount'));

        $rule->validate('totalAmount', 16, function ($message) {
            $this->assertEquals('The Total Amount must be at least 100.00.', $message);
        });

        $rule->validate('amount', 'invalid', function ($message) {
            $this->assertEquals('The Total Amount must be a valid numeric value.', $message);
        });
    }

    public function testMaxValueRule(): void
    {
        $rule = new MaxValueRule(10000, new MoneyInput('amount'));

        $rule->validate('amount', 30000, function ($message) {
            $this->assertEquals('The Amount must be less than or equal to 100.00.', $message);
        });


        $rule->validate('amount', 'invalid', function ($message) {
            $this->assertEquals('The Amount must be a valid numeric value.', $message);
        });
    }
}

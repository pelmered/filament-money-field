<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Casts;

use Illuminate\Database\Eloquent\Model;
use Money\Currency as MoneyCurrency;
use Pelmered\FilamentMoneyField\Casts\CurrencyCast;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TestModel extends Model
{
    protected $casts = [
        'currency' => CurrencyCast::class,
    ];

    protected $fillable = ['currency'];
}

class CurrencyCastTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['filament-money-field.currency_cast_to' => Currency::class]);
    }

    #[Test]
    public function it_casts_to_currency_object()
    {
        $model = new TestModel(['currency' => 'USD']);

        $this->assertInstanceOf(Currency::class, $model->currency);
        $this->assertEquals('USD', $model->currency->getCode());
    }

    #[Test]
    public function it_casts_to_money_currency_when_configured()
    {
        config(['filament-money-field.currency_cast_to' => MoneyCurrency::class]);

        $model = new TestModel(['currency' => 'EUR']);

        $this->assertInstanceOf(MoneyCurrency::class, $model->currency);
        $this->assertEquals('EUR', $model->currency->getCode());
    }

    #[Test]
    public function it_handles_null_values()
    {
        $model = new TestModel(['currency' => null]);

        $this->assertNull($model->currency);
    }

    #[Test]
    public function it_sets_currency_from_currency_instance()
    {
        $model           = new TestModel;
        $model->currency = Currency::fromCode('SEK');

        $this->assertEquals('SEK', $model->getAttributes()['currency']);
    }

    #[Test]
    public function it_sets_currency_from_money_currency_instance()
    {
        $model           = new TestModel;
        $model->currency = new MoneyCurrency('GBP');

        $this->assertEquals('GBP', $model->getAttributes()['currency']);
    }
}

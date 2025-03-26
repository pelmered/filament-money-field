<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Casts;

use Illuminate\Database\Eloquent\Model;
use Money\Currency as MoneyCurrency;
use Pelmered\FilamentMoneyField\Casts\CurrencyCast;
use Pelmered\FilamentMoneyField\Casts\MoneyCast;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Pelmered\FilamentMoneyField\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/*
class TestModel extends Model
{
    protected $casts = [
        //'price'          => MoneyCast::class,
        'price2'          => MoneyCast::class,
        'price_currency'  => CurrencyCast::class,
        'amount'          => MoneyCast::class,
        'amount_currency' => CurrencyCast::class,
    ];

    protected $fillable = ['price', 'price_currency'];
}
*/
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
        $post = Post::factory()->make([
            'price'          => 23523,
            'price_currency' => 'USD'
        ]);

        //$model = TestModel::(['amount' => 23523, 'price_currency' => 'SEK']);

        //$postdd($model, $model->getAttributes());

        $this->assertInstanceOf(Currency::class, $post->price_currency);
        $this->assertEquals('USD', $post->price_currency->getCode());
    }

    #[Test]
    public function it_casts_to_money_currency_when_configured()
    {
        config(['filament-money-field.currency_cast_to' => MoneyCurrency::class]);

        $model = Post::factory()->make(['price_currency' => 'EUR']);

        $this->assertInstanceOf(MoneyCurrency::class, $model->price_currency);
        $this->assertEquals('EUR', $model->price_currency->getCode());
    }

    #[Test]
    public function it_handles_null_values()
    {
        $model = Post::factory()->make(['currency' => null]);

        $this->assertNull($model->currency);
    }

    #[Test]
    public function it_sets_currency_from_currency_instance()
    {
        $model           = Post::factory()->make();
        $model->currency = Currency::fromCode('SEK');

        $this->assertEquals('SEK', $model->getAttributes()['currency']);
    }

    #[Test]
    public function it_sets_currency_from_money_currency_instance()
    {
        $model           = Post::factory()->make();
        $model->currency = new MoneyCurrency('GBP');

        $this->assertEquals('GBP', $model->getAttributes()['currency']);
    }
}

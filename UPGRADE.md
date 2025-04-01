
# Upgrade from 1.* to 2.*

### Add Model cats for money fields (optional but strongly recommended)

_-As of now, this is required, will be made optional in the future.-_

Each money column should have a cast that casts the column to a Money object and the currency column should have a cast that casts the column to a Currency object

```php
use Pelmered\FilamentMoneyField\Casts\CurrencyCast;
use Pelmered\FilamentMoneyField\Casts\MoneyCast;

protected function casts(): array
{
    return [
        'price' => MoneyCast::class,
        'price_currency' => CurrencyCast::class,
        'another_price' => MoneyCast::class,
        'another_price_currency' => CurrencyCast::class,
    ];
}
```
Or as a property:
```php
    protected $casts = [
        'price' => MoneyCast::class,
        'price_currency' => CurrencyCast::class,
        'another_price' => MoneyCast::class,
        'another_price_currency' => CurrencyCast::class,
    ];
```

Value objects are great in most cases, but if you don't want to use them in your code, you can add an [accessor](https://laravel.com/docs/12.x/eloquent-mutators#accessors-and-mutators) for getting the raw values:
```php
protected function price(): Attribute
{
    return Attribute::make(
        get: static fn (string $value) => $value
    );
}
protected function priceCurrency(): Attribute
{
    return Attribute::make(
        get: static fn (string $value) => $value,
    );
}
````


### Add currency columns

Each money column needs a corresponding currency column with the name {money_column}_currency

For new columns
```php
Schema::table('tablename', function (Blueprint $table) {
    $table->money('price'); // This will create two columns, 'price' (integer) and 'price_currency' (char(3))
});
```
For changing existing columns, in this case a column called `price`.
```php
Schema::table('tablename', function (Blueprint $table) {
    $table->char('price_currency', 3)->after('price')->change();
    $this->index(['price', 'price_currency']);
});
```
Don't forget to run your migrations. 

### Config changes

Recommended approach is to make a backup of your current config, and copy in [the new config](config/filament-money-field.php). Then merge the values that you have changed.

## Configure available currencies

You need to configure the available currencies in the `filament-money-field.php` config or in your `.env` file.

In the config file you can configure like this:

```php
    'available_currencies' => [
        'USD',
        'EUR',
        'GBP',
    ],
```

In the `.env` file you can configure like this:

```env
MONEY_AVAILABLE_CURRENCIES=USD,EUR,GBP
```

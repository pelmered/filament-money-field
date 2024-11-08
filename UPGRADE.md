
# Upgrade from 1.*

### For full support, add Money casts for your money fields.

Each money column needs a corresponding currency column with the name {money_column}_currency

For new columns
```
Schema::table('tablename', function (Blueprint $table) {
    $table->money('price'); // This will create two columns, 'price' (integer) and 'price_currency' (char(3))
});
```
For changing existing columns, in this case a column called `price`.
```
Schema::table('tablename', function (Blueprint $table) {
    $table->char('price_currency', 3)->after('price')->change();
    $this->index(['price', 'price_currency']);
});
```

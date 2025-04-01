<?php

namespace Pelmered\FilamentMoneyField\Currencies;

use Money\Currency as MoneyCurrency;
use Money\Money;
use Pelmered\FilamentMoneyField\Exceptions\UnsupportedCurrency;
use PhpStaticAnalysis\Attributes\Param;

class Currency implements \Stringable
{
    #[Param(code: 'string', name: 'string')]
    public function __construct(
        public string $code,
        public string $name,
        public ?int $minorUnit = null,
    ) {
        $this->code = strtoupper($code);
    }

    public static function fromCode(string $currencyCode): self
    {
        $currencyCode = strtoupper($currencyCode);

        return CurrencyRepository::getAvailableCurrencies()->get($currencyCode)
               ?? throw new UnsupportedCurrency($currencyCode);
    }

    public static function fromMoneyCurrency(MoneyCurrency $currency): self
    {
        return static::fromCode($currency->getCode());
    }

    public static function fromMoney(Money $money): self
    {
        return static::fromMoneyCurrency($money->getCurrency());
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Checks whether this currency is the same as an other.
     */
    public function equals(Currency $other): bool
    {
        return $this->code === $other->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function jsonSerialize(): string
    {
        return $this->code;
    }

    public function toMoneyCurrency(): MoneyCurrency
    {
        if ($this->code === '' || $this->code === '0') {
            return new MoneyCurrency('USD');
        }

        return new MoneyCurrency($this->code);
    }
}

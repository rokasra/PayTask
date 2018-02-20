<?php

namespace App\Service;

use App\Document\Money;
use App\Interfaces\CurrencyInterface;

/**
 * App\Service\CurrencyConverter
 */
class CurrencyConverter implements CurrencyInterface
{
    /**
     * @param Money  $money
     * @param string $currency
     *
     * @return Money
     */
    public function convert(Money $money, $currency)
    {
        if ($money->getCurrency() === $currency) {
            return $money;
        }
        $currencyRate = $this->getCurrencyRate($money->getCurrency(), $currency);
        if ($currencyRate === null) {
            return $money;
        }
        $result = new Money(
            $money->getAmount() * $currencyRate,
            $currency
        );

        return $result;
    }

    /**
     * @param string $currency
     * @param string $actualCurrency
     *
     * @return float|null
     */
    protected function getCurrencyRate($currency, $actualCurrency)
    {
        $selectCurrency = $actualCurrency;
        if ($actualCurrency == CurrencyInterface::BASE_CURRENCY) {
            $selectCurrency = $currency;
        }

        if (!array_key_exists($selectCurrency, CurrencyInterface::CURRENCY_RATE)) {

            return null;
        }

        $rate = CurrencyInterface::CURRENCY_RATE[$selectCurrency];
        if ($actualCurrency == CurrencyInterface::BASE_CURRENCY) {
            $rate = 1 / $rate;
        }

        return $rate;
    }
}
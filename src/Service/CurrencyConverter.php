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
     * @param Money $money
     *
     * @return Money|null
     */
    public function convertToBase(Money $money)
    {
        if ($money->getCurrency() === CurrencyInterface::BASE_CURRENCY) {
            return $money;
        }

        $currencyRate = $this->getCurrencyRate($money->getCurrency());
        if ($currencyRate === null) {

            return null;
        }
        $result = new Money(
            $money->getAmount() / $currencyRate,
            CurrencyInterface::BASE_CURRENCY
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(Money $money, $currency)
    {
        if ($money->getCurrency() === $currency) {
            return $money;
        }
        $currencyRate = $this->getCurrencyRate($currency);
        if ($currencyRate === null) {
            return null;
        }
        $result = new Money(
            $money->getAmount() * $currencyRate,
            $currency
        );

        return $result;
    }

    /**
     * @param string $currency
     *
     * @return string|null
     */
    protected function getCurrencyRate($currency)
    {
        if (!array_key_exists($currency, self::CURRENCY_RATE)) {

            return null;
        }

        return self::CURRENCY_RATE[$currency];
    }
}
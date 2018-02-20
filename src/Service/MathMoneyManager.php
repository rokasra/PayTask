<?php

namespace App\Service;

use App\Document\Money;

/**
 * App\Service\MathMoneyManager
 */
class MathMoneyManager
{
    /**
     * @var CurrencyConverter
     */
    protected $currencyConverter;

    /**
     * Class constructor
     *
     * @param CurrencyConverter $currencyConverter
     */
    public function __construct(
        CurrencyConverter $currencyConverter
    ) {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @param Money $left
     * @param Money $right
     *
     * @return Money
     */
    public function add(Money $left, Money $right)
    {
        if (!$this->validateCurrency($left, $right)) {
            $right = $this->currencyConverter->convert($right, $left->getCurrency());
        }

        $result = $left->getAmount() + $right->getAmount();

        return new Money($result, $left->getCurrency());
    }

    /**
     * @param Money $left
     * @param Money $right
     *
     * @return Money
     */
    public function sub(Money $left, Money $right)
    {
        if (!$this->validateCurrency($left, $right)) {
            $right = $this->currencyConverter->convert($right, $left->getCurrency());
        }

        $result = $left->getAmount() - $right->getAmount();

        return new Money($result, $left->getCurrency());
    }

    /**
     * Multiply two Money objects
     *
     * @param Money $left
     * @param float $multiplicand
     *
     * @return Money
     */
    public function mul(Money $left, $multiplicand)
    {
        $result = $left->getAmount() * $multiplicand;

        return new Money($result, $left->getCurrency());
    }

    /**
     * @param Money $left
     * @param Money $right
     *
     * @return int
     */
    public function compare(Money $left, Money $right)
    {
        if (!$this->validateCurrency($left, $right)) {
            $right = $this->currencyConverter->convert($right, $left->getCurrency());
        }

        $result = $left->getAmount() - $right->getAmount();

        return $result > 0 ? 1 : ($result < 0 ? -1 : 0);
    }

    /**
     * @param Money $left
     * @param int   $precision
     *
     * @return Money
     */
    public function ceil(Money $left, $precision)
    {
        $multiplier = pow(10, $precision);
        $result = ceil(round($left->getAmount() * $multiplier, 5)) / $multiplier;

        return new Money(
            $result,
            $left->getCurrency()
        );
    }

    /**
     * @param Money $left
     * @param Money $right
     *
     * @return bool
     */
    protected function validateCurrency(Money $left, Money $right)
    {
        if ($left->getCurrency() !== $right->getCurrency()) {
            return false;
        }

        return true;
    }
}

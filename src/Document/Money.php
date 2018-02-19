<?php

namespace App\Document;

/**
 * App\Document\Money
 */
class Money
{
    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * Class constructor
     *
     * @param float|int $amount
     * @param string    $currency
     */
    public function __construct($amount, $currency)
    {
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    /**
     * Getter of Amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Getter of Currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Setter of Amount
     *
     * @param float $amount
     *
     * @return static
     */
    public function setAmount($amount)
    {
        $this->amount = (float) $amount;

        return $this;
    }

    /**
     * Setter of Currency
     *
     * @param string $currency
     *
     * @return static
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s %s', $this->getAmount(), $this->getCurrency());
    }
}

<?php

namespace App\Document;

/**
 * App\Document\Statement
 */
class Statement
{
    const INPUT_TYPE_NATURAL = 'natural';
    const INPUT_TYPE_LEGAL = 'legal';

    const INPUT_CASH_IN = 'cash_in';
    const INPUT_CASH_OUT = 'cash_out';

    /**
     * @var \DateTimeInterface
     */
    protected $date;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $cash;

    /**
     * @var Money
     */
    protected $money;

    /**
     * @var Money
     */
    protected $commissions;

    /**
     * @var string
     */
    protected $hash;

    /**
     * Getter of Date
     *
     * @return \DateTimeInterface
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Setter of Date
     *
     * @param \DateTimeInterface $date
     *
     * @return static
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Getter of ClientId
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Setter of ClientId
     *
     * @param string $clientId
     *
     * @return static
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Getter of Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter of Type
     *
     * @param string $type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Getter of Cash
     *
     * @return string
     */
    public function getCash()
    {
        return $this->cash;
    }

    /**
     * Setter of Cash
     *
     * @param string $cash
     *
     * @return static
     */
    public function setCash($cash)
    {
        $this->cash = $cash;

        return $this;
    }

    /**
     * Getter of Money
     *
     * @return Money
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Setter of Money
     *
     * @param Money $money
     *
     * @return static
     */
    public function setMoney($money)
    {
        $this->money = $money;

        return $this;
    }

    /**
     * Getter of Commissions
     *
     * @return Money
     */
    public function getCommissions()
    {
        return $this->commissions;
    }

    /**
     * Setter of Commissions
     *
     * @param Money $commissions
     *
     * @return static
     */
    public function setCommissions($commissions)
    {
        $this->commissions = $commissions;

        return $this;
    }

    /**
     * Getter of Hash
     *
     * @return string
     */
    public function getHash()
    {
        if (!$this->hash) {
            $this->hash = md5(
                sprintf(
                    '%s%s%s%s%s',
                    $this->getDate()->format('Y-m-d'),
                    $this->getClientId(),
                    $this->getType(),
                    $this->getCash(),
                    $this->getMoney()->__toString()
                )
            );
        }

        return $this->hash;
    }
}

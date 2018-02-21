<?php

namespace App\Service;

use App\Document\Money;
use App\Document\Statement;
use App\Interfaces\CurrencyInterface;

/**
 * App\Service\CommissionsManager
 */
class CommissionsManager implements CurrencyInterface
{
    /**
     * @var CurrencyConverter
     */
    protected $currencyConverter;
    /**
     * @var MathMoneyManager
     */
    protected $mathMoneyManager;

    /**
     * @var Statement[]
     */
    protected $statements = [];

    /**
     * Class constructor
     *
     * @param CurrencyConverter $currencyConverter
     * @param MathMoneyManager  $mathMoneyManager
     */
    public function __construct(
        CurrencyConverter $currencyConverter,
        MathMoneyManager $mathMoneyManager
    ) {
        $this->currencyConverter = $currencyConverter;
        $this->mathMoneyManager = $mathMoneyManager;
    }

    /**
     * @param array $data
     *
     * @return Statement[]
     */
    public function calculate($data)
    {
        foreach ($data as $row) {
            if (!$this->validateRow($row)) {
                continue;
            }
            $this->statements[] = (new Statement())
                ->setDate(new \DateTime($row[0]))
                ->setClientId($row[1])
                ->setType($row[2])
                ->setCash($row[3])
                ->setMoney(new Money($row[4], $row[5]));
        }

        foreach ($this->statements as $statement) {
            if ($statement->getCash() == Statement::INPUT_CASH_IN) {
                $this->setCashIn($statement);
            } elseif ($statement->getCash() == Statement::INPUT_CASH_OUT && $statement->getType() == Statement::INPUT_TYPE_NATURAL) {
                $this->setCashOutNatural($statement);
            } else {
                $this->setCashOutLegal($statement);
            }
        }

        return $this->statements;
    }

    /**
     * @param Statement $statement
     */
    protected function setCashIn($statement)
    {
        $maxAmount = new Money(5, CurrencyInterface::BASE_CURRENCY);
        $taxPercent = 0.0003;

        $commissions = $this->mathMoneyManager->mul($statement->getMoney(), $taxPercent);
        if ($this->mathMoneyManager->compare($commissions, $maxAmount) < 0) {
            $this->setCommissions($statement, $commissions);
        } else {
            $commissions = $this->currencyConverter->convert(
                $maxAmount,
                $statement->getMoney()->getCurrency()
            );
            $this->setCommissions($statement, $commissions);
        }
    }

    /**
     * @param Statement $statement
     */
    protected function setCashOutLegal($statement)
    {
        $minAmount = new Money(0.5, CurrencyInterface::BASE_CURRENCY);
        $taxPercent = 0.003;

        $commissions = $this->mathMoneyManager->mul($statement->getMoney(), $taxPercent);
        if ($this->mathMoneyManager->compare($commissions, $minAmount) > 0) {
            $statement->setCommissions($commissions);
        } else {
            $statement->setCommissions(
                $this->currencyConverter->convert(
                    $minAmount,
                    $statement->getMoney()->getCurrency()
                )
            );
        }
    }

    /**
     * @param Statement $statement
     */
    protected function setCashOutNatural($statement)
    {
        $transactionsFreeLimit = 3;
        $maxAmount = new Money(1000, CurrencyInterface::BASE_CURRENCY);
        $taxPercent = 0.003;

        $clientStatements = $this->getActualStatements($statement);
        if (count($clientStatements) >= $transactionsFreeLimit) {
            $commissions = $this->mathMoneyManager->mul($statement->getMoney(), $taxPercent);
        } else {
            $sumCash = new Money(0, CurrencyInterface::BASE_CURRENCY);
            foreach ($clientStatements as $cStatment) {
                $sumCash = $this->mathMoneyManager->add($sumCash, $cStatment->getMoney());
            }
            $allCashMoney = $this->mathMoneyManager->add($statement->getMoney(), $sumCash);
            if ($this->mathMoneyManager->compare($sumCash, $maxAmount) >= 0) {
                $commissions = $this->mathMoneyManager->mul($statement->getMoney(), $taxPercent);
            } elseif ($this->mathMoneyManager->compare($allCashMoney, $maxAmount) < 0) {
                $commissions = new Money(0, $statement->getMoney()->getCurrency());
            } else {
                $commissions = $this->mathMoneyManager->sub($allCashMoney, $maxAmount);
                $commissions = $this->mathMoneyManager->mul($commissions, $taxPercent);
            }
        }

        $this->setCommissions($statement, $commissions);
    }

    /**
     * @param Statement $statement
     *
     * @return Statement[]
     */
    protected function getActualStatements($statement)
    {
        $monday = (new \DateTime(
            sprintf('Monday this week %s', $statement->getDate()->format('Y-m-d'))
        ));
        $return = [];
        foreach ($this->statements as $cacheStatement) {
            if ($statement->getHash() == $cacheStatement->getHash()) {
                break;
            }

            if (
                $cacheStatement->getCash() == Statement::INPUT_CASH_OUT &&
                $cacheStatement->getDate() >= $monday &&
                $cacheStatement->getClientId() == $statement->getClientId()
            ) {
                $return[] = $cacheStatement;
            }
        }

        return $return;
    }

    /**
     * @param $row
     *
     * @return bool
     */
    protected function validateRow($row)
    {
        if (
            empty($row) ||
            count($row) != 6 ||
            !preg_match('~^\d{4}\-\d{2}\-\d{2}$~', $row[0]) ||
            !in_array($row[2], [Statement::INPUT_TYPE_NATURAL, Statement::INPUT_TYPE_LEGAL]) ||
            !in_array($row[3], [Statement::INPUT_CASH_IN, Statement::INPUT_CASH_OUT]) ||
            (float) $row[4] <= 0 ||
            !array_key_exists($row[5], CurrencyInterface::CURRENCY_RATE)
        ) {

            return false;
        }

        return true;
    }

    /**
     * @param Statement $statement
     * @param Money     $money
     *
     * @return static
     */
    protected function setCommissions(Statement $statement, Money $money)
    {
        $precision = CurrencyInterface::CURRENCY_PRECISION[$money->getCurrency()];
        $money = $this->mathMoneyManager->ceil($money, $precision);
        $statement->setCommissions($money);

        return $this;
    }
}
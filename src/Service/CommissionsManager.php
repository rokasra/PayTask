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
    const INPUT_TYPE_NATURAL = 'natural';
    const INPUT_TYPE_LEGAL = 'legal';

    const INPUT_CASH_IN = 'cash_in';
    const INPUT_CASH_OUT = 'cash_out';

    /**
     * @var CurrencyConverter
     */
    protected $currencyConverter;

    /**
     * @var Statement[]
     */
    protected $statements = [];

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
            if ($statement->getCash() == self::INPUT_CASH_IN) {
                $this->setCashIn($statement);
            } elseif ($statement->getCash() == self::INPUT_CASH_OUT && $statement->getType() == self::INPUT_TYPE_NATURAL) {
                $this->setCashOutNatural($statement);
            } elseif ($statement->getCash() == self::INPUT_CASH_OUT && $statement->getType() == self::INPUT_TYPE_LEGAL) {
                $this->setCashOutLegal($statement);
            } else {
                continue;
            }
        }

        return $this->statements;
    }

    /**
     * @param Statement $statement
     */
    protected function setCashIn($statement)
    {
        $maxAmount = 5;
        $taxPercent = 0.0003;
        $commissions = $statement->getMoney()->getAmount() * $taxPercent;
        $statementCurrency = $statement->getMoney()->getCurrency();
        $commissionsConverted = $this->currencyConverter->convertToBase(new Money($commissions, $statementCurrency));

        if ($commissionsConverted->getAmount() < $maxAmount) {
            $statement->setCommissions(new Money($commissions, $statementCurrency));
        } else {
            $statement->setCommissions(
                $this->currencyConverter->convert(
                    new Money($maxAmount, self::BASE_CURRENCY),
                    $statementCurrency
                )
            );
        }
    }

    /**
     * @param Statement $statement
     */
    protected function setCashOutLegal($statement)
    {
        $minAmount = 0.5;
        $taxPercent = 0.003;
        $commissions = $statement->getMoney()->getAmount() * $taxPercent;
        $statementCurrency = $statement->getMoney()->getCurrency();
        $commissionsConverted = $this->currencyConverter->convertToBase(new Money($commissions, $statementCurrency));

        if ($commissionsConverted->getAmount() > $minAmount) {
            $statement->setCommissions(new Money($commissions, $statementCurrency));
        } else {
            $statement->setCommissions(
                $this->currencyConverter->convert(
                    new Money($minAmount, self::BASE_CURRENCY),
                    $statementCurrency
                )
            );
        }
    }

    /**
     * @param Statement $statement
     */
    protected function setCashOutNatural($statement)
    {
        $freeTransactions = 3;
        $maxAmount = 1000;
        $taxPercent = 0.003;

        $clientStatements = $this->getActualStatements($statement);

        if (count($clientStatements) > $freeTransactions) {
            $commissions = $statement->getMoney()->getAmount() * $taxPercent;
            $statement->setCommissions(new Money($commissions, $statement->getMoney()->getCurrency()));
        } else {
//            TODO:
        }
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
                $cacheStatement->getCash() == self::INPUT_CASH_OUT &&
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
            count($row) != 6 ||
            !preg_match('~^\d{4}\-\d{2}\-\d{2}$~', $row[0]) ||
            !in_array($row[2], [self::INPUT_TYPE_NATURAL, self::INPUT_TYPE_LEGAL]) ||
            !in_array($row[3], [self::INPUT_CASH_IN, self::INPUT_CASH_OUT]) ||
            (float) $row[4] <= 0 ||
            !array_key_exists($row[5], CurrencyInterface::CURRENCY_RATE)
        ) {

            return false;
        }

        return true;
    }
}
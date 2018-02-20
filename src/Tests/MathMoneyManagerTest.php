<?php

namespace App\Tests;

use App\Document\Money;
use App\Service\CurrencyConverter;
use App\Service\MathMoneyManager;

/**
 * App\Tests\CommissionCalculatorManagerTest
 */
class MathMoneyManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CurrencyConverter
     */
    protected $currencyConverter;

    /**
     * @var MathMoneyManager
     */
    protected $moneyManager;

    /**
     * Test add
     */
    public function testAdd()
    {
        $left = new Money(10, 'EUR');
        $right = new Money(15, 'EUR');
        $result = $this->moneyManager->add($left, $right);
        $this->assertEquals(new Money(25, 'EUR'), $result);

        $left = new Money(10, 'EUR');
        $right = new Money(15, 'USD');
        $result = $this->moneyManager->add($left, $right);
        $this->assertEquals(new Money(25, 'EUR'), $result);
    }

    /**
     * Test sub
     */
    public function testSub()
    {
        $left = new Money(30, 'EUR');
        $right = new Money(20, 'EUR');
        $result = $this->moneyManager->sub($left, $right);
        $this->assertEquals(new Money(10, 'EUR'), $result);

        $left = new Money(20, 'EUR');
        $right = new Money(15, 'USD');
        $result = $this->moneyManager->sub($left, $right);
        $this->assertEquals(new Money(5, 'EUR'), $result);
    }

    /**
     * Test mul
     */
    public function testMul()
    {
        $left = new Money(30, 'EUR');
        $multiplicand = 10;
        $result = $this->moneyManager->mul($left, $multiplicand);
        $this->assertEquals(new Money(300, 'EUR'), $result);
    }

    /**
     * Test compare
     */
    public function testCompare()
    {
        $left = new Money(30, 'EUR');
        $right = new Money(20, 'EUR');
        $result = $this->moneyManager->compare($left, $right);
        $this->assertEquals(1, $result);

        $left = new Money(20, 'EUR');
        $right = new Money(20, 'USD');
        $result = $this->moneyManager->compare($left, $right);
        $this->assertEquals(0, $result);

        $left = new Money(5, 'EUR');
        $right = new Money(10, 'USD');
        $result = $this->moneyManager->compare($left, $right);
        $this->assertEquals(-1, $result);
    }



    /**
     * Test ceil
     */
    public function testCeil()
    {
        $left = new Money(30, 'EUR');
        $precision = 2;
        $result = $this->moneyManager->ceil($left, $precision);
        $this->assertEquals(new Money(30, 'EUR'), $result);

        $left = new Money(15.5456645564, 'EUR');
        $precision = 2;
        $result = $this->moneyManager->ceil($left, $precision);
        $this->assertEquals(new Money(15.55, 'EUR'), $result);

        $left = new Money(15.541111, 'EUR');
        $precision = 3;
        $result = $this->moneyManager->ceil($left, $precision);
        $this->assertEquals(new Money(15.542, 'EUR'), $result);
    }

    /**
     * Sets up test
     */
    protected function setUp()
    {
        $this->currencyConverter = $this->getMockBuilder(CurrencyConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->currencyConverter->expects($this->any())
            ->method('convert')->will(
                $this->returnCallback(
                    function (Money $first, $currency) {
                        return new Money($first->getAmount(), $currency);
                    }
                )
            );

        $this->moneyManager = new MathMoneyManager($this->currencyConverter);
    }
}

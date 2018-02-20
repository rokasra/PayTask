<?php

namespace App\Tests;

use App\Document\Money;
use App\Service\CommissionsManager;
use App\Service\CurrencyConverter;
use App\Service\MathMoneyManager;

/**
 * App\Tests\CommissionCalculatorManagerTest
 */
class CommissionCalculatorManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CurrencyConverter
     */
    protected $currencyConverter;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MathMoneyManager
     */
    protected $mathMoneyManager;

    /**
     * @var CommissionsManager
     */
    protected $manager;

    /**
     * @param array $data
     * @param array $expectResults
     *
     * @dataProvider dataProvider
     */
    public function testCalculate($data, $expectResults)
    {
        $results = $this->manager->calculate($data);

        $arr = [];
        foreach ($results as $result) {
            array_push($arr, $result->getCommissions()->getAmount());
        }

        for ($i = 0; $i < count($results); $i++) {
            $this->assertEquals($expectResults[$i], $results[$i]->getCommissions()->getAmount());
        }
    }

    /**
     * Test invalid data
     */
    public function testCalculateInvalidData()
    {
        $data = [
            ['2015-01-01', '1', 'natural', 'cash_out', '1200.00', 'SEK']
        ];

        $results = $this->manager->calculate($data);

        $this->assertCount(0, $results);
    }

    /**
     * Test Code from link provider
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'data' => [
                    ['2015-01-01', '1', 'natural', 'cash_out', '1200.00', 'EUR'],
                    ['2016-12-31', '1', 'natural', 'cash_out', '1000.00', 'EUR'],
                    ['2016-01-01', '1', 'natural', 'cash_out', '1000.00', 'EUR'],
                    ['2016-01-05', '2', 'natural', 'cash_in', '200.00', 'EUR'],
                    ['2016-01-06', '3', 'legal', 'cash_out', '300.00', 'EUR'],
                    ['2016-01-06', '2', 'natural', 'cash_out', '30000', 'JPY'],
                    ['2016-01-07', '2', 'natural', 'cash_out', '1000.00', 'EUR'],
                    ['2016-01-07', '2', 'natural', 'cash_out', '100.00', 'USD'],
                    ['2016-01-10', '2', 'natural', 'cash_out', '100.00', 'EUR'],
                    ['2016-01-10', '3', 'legal', 'cash_in', '1000000.00', 'EUR'],
                    ['2016-01-10', '4', 'legal', 'cash_out', '1000.00', 'EUR'],
                    ['2016-02-15', '2', 'natural', 'cash_out', '300.00', 'EUR'],
                    ['2016-02-19', '2', 'natural', 'cash_out', '3000000', 'JPY'],
                ],
                'result' => [0.60, 0.00, 3.00, 0.06, 0.90, 87, 3, 0.30, 0.30, 5.00, 3.00, 0.00, 8998],
            ],
            [
                'data' => [
                    ['2018-02-06', '1', 'natural', 'cash_out', '300.00', 'EUR'],
                    ['2018-02-06', '1', 'natural', 'cash_out', '400.00', 'EUR'],
                    ['2018-02-07', '1', 'natural', 'cash_out', '200.00', 'EUR'],
                    ['2018-02-09', '1', 'natural', 'cash_out', '500.00', 'EUR'],
                ],
                'result' => [0.00, 0.00, 0.00, 1.5],
            ],
            [
                'data' => [
                    ['2018-02-06', '5', 'legal', 'cash_out', '50.00', 'JPY'],
                ],
                'result' => [0.5],
            ],
        ];
    }

    /**
     * Sets up test
     */
    protected function setUp()
    {
        $this->currencyConverter = $this->getMockBuilder(CurrencyConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mathMoneyManager = $this->getMockBuilder(MathMoneyManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mathMoneyManager->expects($this->any())
            ->method('add')->will(
                $this->returnCallback(
                    function (Money $first, Money $last) {
                        return new Money($first->getAmount() + $last->getAmount(), $first->getCurrency());
                    }
                )
            );

        $this->mathMoneyManager->expects($this->any())
            ->method('sub')->will(
                $this->returnCallback(
                    function (Money $first, Money $last) {
                        return new Money($first->getAmount() - $last->getAmount(), $first->getCurrency());
                    }
                )
            );

        $this->mathMoneyManager->expects($this->any())
            ->method('mul')->will(
                $this->returnCallback(
                    function (Money $first, $multiplicand) {
                        return new Money($first->getAmount() * $multiplicand, $first->getCurrency());
                    }
                )
            );

        $this->mathMoneyManager->expects($this->any())
            ->method('compare')->will(
                $this->returnCallback(
                    function (Money $first, Money $last) {
                        $result = $result = $first->getAmount() - $last->getAmount();
                        return $result > 0 ? 1 : ($result < 0 ? -1 : 0);
                    }
                )
            );

        $this->mathMoneyManager->expects($this->any())
            ->method('ceil')->will(
                $this->returnCallback(
                    function (Money $first, $precision) {
                        return new Money(round($first->getAmount(), $precision), $first->getCurrency());
                    }
                )
            );

        $this->currencyConverter->expects($this->any())
            ->method('convert')->will(
                $this->returnCallback(
                    function (Money $first, $currency) {
                        return new Money($first->getAmount(), $currency);
                    }
                )
            );

        $this->manager = new CommissionsManager(
            $this->currencyConverter,
            $this->mathMoneyManager
        );
    }
}
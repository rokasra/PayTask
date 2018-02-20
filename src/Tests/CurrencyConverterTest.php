<?php

namespace App\Tests;

use App\Document\Money;
use App\Service\CurrencyConverter;

/**
 * App\Tests\CurrencyConverterTest
 */
class CurrencyConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CurrencyConverter
     */
    protected $converter;

    /**
     * @param Money  $data
     * @param string $currency
     * @param Money  $expect
     *
     * @dataProvider dataProvider
     */
    public function testConvert($data, $currency, $expect)
    {
        $result = $this->converter->convert($data, $currency);

        $this->assertEquals($expect, $result);
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
                new Money(10, 'EUR'),
                'EUR',
                new Money(10, 'EUR'),
            ],
            [
                new Money(15, 'EUR'),
                'SEK',
                new Money(15, 'EUR'),
            ],
            [
                new Money(1497, 'USD'),
                'EUR',
                new Money(1000, 'EUR'),
            ],
        ];
    }

    /**
     * Sets up test
     */
    protected function setUp()
    {
        $this->converter = new CurrencyConverter();
    }
}

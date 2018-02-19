<?php

namespace App\Interfaces;

/**
 * App\Interfaces\CurrencyConverter
 */
interface CurrencyInterface
{
    const BASE_CURRENCY = 'EUR';
    const CURRENCY_RATE = [
        'EUR' => 1,
        'USD' => 1.497,
        'JPY' => 129.53,
    ];
}
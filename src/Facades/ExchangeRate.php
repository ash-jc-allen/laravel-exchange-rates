<?php

namespace AshAllenDesign\LaravelExchangeRates\Facades;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array currencies(array $currencies = [])
 * @method static string exchangeRate(string $from, string $to, ?Carbon $date = null)
 * @method static array exchangeRateBetweenDateRange(string $from, string $to, Carbon $date, Carbon $endDate, array $conversions = [])
 * @method static float convert(int $value, string $from, string $to, Carbon $date = null)
 * @method static float convertBetweenDateRange(int $value, string $from, string $to, Carbon $date, Carbon $endDate, array $conversions = [])
 * @method static self shouldBustCache(bool $bustCache = true)
 *
 * @see \AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate
 */
class ExchangeRate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'exchange-rate';
    }
}

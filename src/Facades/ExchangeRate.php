<?php

namespace AshAllenDesign\LaravelExchangeRates\Facades;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array currencies(array $currencies = [])
 * @method static string|array exchangeRate(string $from, $to, ?Carbon $date = null)
 * @method static array exchangeRateBetweenDateRange(string $from, $to, Carbon $date, Carbon $endDate, array $conversions = [])
 * @method static float|array convert(int $value, string $from, $to, Carbon $date = null)
 * @method static array convertBetweenDateRange(int $value, string $from, $to, Carbon $date, Carbon $endDate, array $conversions = [])
 * @method static self shouldBustCache(bool $bustCache = true)
 * @method static self shouldCache(bool $shouldCache = true)
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

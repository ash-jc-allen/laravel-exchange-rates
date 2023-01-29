<?php

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo;

use AshAllenDesign\LaravelExchangeRates\Classes\CacheRepository;
use AshAllenDesign\LaravelExchangeRates\Drivers\Support\SharedDriverLogicHandler;
use AshAllenDesign\LaravelExchangeRates\Exceptions\ExchangeRateException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Carbon\Carbon;
use Exception;

/**
 * @see https://exchangeratesapi.io/
 */
class ExchangeRatesApiIoDriver implements ExchangeRateDriver
{
    private SharedDriverLogicHandler $sharedDriverLogicHandler;

    /**
     * ExchangeRate constructor.
     *
     * @param  RequestBuilder|null  $requestBuilder
     * @param  CacheRepository|null  $cacheRepository
     */
    public function __construct(RequestBuilder $requestBuilder = null, CacheRepository $cacheRepository = null)
    {
        $requestBuilder = $requestBuilder ?? new RequestBuilder();
        $cacheRepository = $cacheRepository ?? new CacheRepository();

        $this->sharedDriverLogicHandler = new SharedDriverLogicHandler(
            $requestBuilder,
            $cacheRepository
        );
    }

    /**
     * Return an array of available currencies that
     * can be used with this package.
     *
     * @param  array  $currencies
     * @return array
     */
    public function currencies(array $currencies = []): array
    {
        return $this->sharedDriverLogicHandler->currencies($currencies);
    }

    /**
     * Return the exchange rate between the $from and $to
     * parameters. If no $date parameter is passed, we
     * use today's date instead. If $to is a string,
     * the exchange rate will be returned as a
     * string. If $to is an array, the rates
     * will be returned within an array.
     *
     * @param  string  $from
     * @param  string|array  $to
     * @param  Carbon|null  $date
     * @return float|string|array
     *
     * @throws InvalidDateException
     * @throws InvalidCurrencyException
     * @throws ExchangeRateException
     */
    public function exchangeRate(string $from, $to, Carbon $date = null)
    {
        return $this->sharedDriverLogicHandler->exchangeRate($from, $to, $date);
    }

    /**
     * Return the exchange rates between the given
     * date range.
     *
     * @param  string  $from
     * @param  string|array  $to
     * @param  Carbon  $date
     * @param  Carbon  $endDate
     * @param  array  $conversions
     * @return array
     *
     * @throws Exception
     */
    public function exchangeRateBetweenDateRange(
        string $from,
               $to,
        Carbon $date,
        Carbon $endDate,
        array $conversions = []
    ): array {
        return $this->sharedDriverLogicHandler->exchangeRateBetweenDateRange($from, $to, $date, $endDate, $conversions);
    }

    /**
     * Return the converted values between the $from and $to
     * parameters. If no $date parameter is passed, we
     * use today's date instead.
     *
     * @param  int  $value
     * @param  string  $from
     * @param  string|array  $to
     * @param  Carbon|null  $date
     * @return float|array
     *
     * @throws InvalidDateException
     * @throws InvalidCurrencyException
     * @throws ExchangeRateException
     */
    public function convert(int $value, string $from, $to, Carbon $date = null)
    {
        return $this->sharedDriverLogicHandler->convert($value, $from, $to, $date);
    }

    /**
     * Return an array of the converted values between the
     * given date range.
     *
     * @param  int  $value
     * @param  string  $from
     * @param  string|array  $to
     * @param  Carbon  $date
     * @param  Carbon  $endDate
     * @param  array  $conversions
     * @return array
     *
     * @throws Exception
     */
    public function convertBetweenDateRange(
        int $value,
        string $from,
        $to,
        Carbon $date,
        Carbon $endDate,
        array $conversions = []
    ): array {
        return $this->sharedDriverLogicHandler->convertBetweenDateRange($value, $from, $to, $date, $endDate, $conversions);
    }

    public function shouldCache(bool $shouldCache = true): ExchangeRateDriver
    {
        $this->sharedDriverLogicHandler->shouldCache($shouldCache);

        return $this;
    }

    public function shouldBustCache(bool $bustCache = true): ExchangeRateDriver
    {
        $this->sharedDriverLogicHandler->shouldBustCache($bustCache);

        return $this;
    }
}

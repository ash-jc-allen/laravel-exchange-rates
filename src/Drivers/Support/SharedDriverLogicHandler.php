<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\Support;

use AshAllenDesign\LaravelExchangeRates\Classes\CacheRepository;
use AshAllenDesign\LaravelExchangeRates\Classes\Validation;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\LaravelExchangeRates\Interfaces\RequestSender;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Several exchange rates APIs are built to follow a similar structure
 * to each other. So we can use the same logic for a large majority
 * of the drivers to reduce duplication of code.
 *
 * @interal
 */
class SharedDriverLogicHandler
{
    /**
     * The object used for making requests to the currency conversion API.
     */
    private RequestSender $requestBuilder;

    /**
     * The repository used for accessing the cache.
     */
    private CacheRepository $cacheRepository;

    /**
     * Whether the exchange rate should be cached after being fetched from the API.
     */
    private bool $shouldCache = true;

    /**
     * Whether the cache should be busted and a new value should be fetched from the API.
     */
    private bool $shouldBustCache = false;

    public function __construct(RequestSender $requestBuilder, CacheRepository $cacheRepository)
    {
        $this->requestBuilder = $requestBuilder;
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * Return an array of available currencies that can be used with this package.
     *
     * @return string[]
     *
     * @throws InvalidArgumentException
     * @throws RequestException
     */
    public function currencies(): array
    {
        $cacheKey = 'currencies';

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $response = $this->requestBuilder->makeRequest('/latest', []);

        $currencies = [$response->get('base')];

        foreach ($response->rates() as $currency => $rate) {
            $currencies[] = $currency;
        }

        if ($this->shouldCache) {
            $this->cacheRepository->storeInCache($cacheKey, $currencies);
        }

        return $currencies;
    }

    /**
     * Return the exchange rate between the $from and $to parameters. If no $date
     * parameter is passed, we use today's date instead. If $to is a string,
     * the exchange rate will be returned as a string. If $to is an array,
     * the rates will be returned within an array.
     *
     * @param  string  $from
     * @param  string|string[]  $to
     * @param  Carbon|null  $date
     * @return float|array<string, float>
     *
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function exchangeRate(string $from, string|array $to, Carbon $date = null): float|array
    {
        $this->validateExchangeRateInput($from, $to, $date);

        if ($from === $to) {
            return 1.0;
        }

        $cacheKey = $this->cacheRepository->buildCacheKey($from, $to, $date ?? Carbon::now());

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            // If the exchange rate has been retrieved from the cache as a
            // string (e.g. "1.23"), then cast it to a float (e.g. 1.23).
            // If we have retrieved the rates for many currencies, it
            // will be an array of floats, so just return it.
            return is_string($cachedExchangeRate)
                ? (float) $cachedExchangeRate
                : $cachedExchangeRate;
        }

        $symbols = is_string($to) ? $to : implode(',', $to);
        $queryParams = ['base' => $from, 'symbols' => $symbols];

        $url = $date
            ? '/'.$date->format('Y-m-d')
            : '/latest';

        $response = $this->requestBuilder->makeRequest($url, $queryParams)->rates();

        $exchangeRate = is_string($to) ? $response[$to] : $response;

        if ($this->shouldCache) {
            $this->cacheRepository->storeInCache($cacheKey, $exchangeRate);
        }

        return $exchangeRate;
    }

    /**
     * Return the exchange rates between the given date range.
     *
     * @param  string  $from
     * @param  string|string[]  $to
     * @param  Carbon  $date
     * @param  Carbon  $endDate
     * @return array<string, float>|array<string, array<string, float>>
     *
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function exchangeRateBetweenDateRange(
        string $from,
        string|array $to,
        Carbon $date,
        Carbon $endDate,
    ): array {
        $this->validateExchangeRateBetweenDateRangeInput($from, $to, $date, $endDate);

        $cacheKey = $this->cacheRepository->buildCacheKey($from, $to, $date, $endDate);

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $conversions = $from === $to
            ? $this->exchangeRateDateRangeResultWithSameCurrency($date, $endDate)
            : $this->makeRequestForExchangeRates($from, $to, $date, $endDate);

        if ($this->shouldCache) {
            $this->cacheRepository->storeInCache($cacheKey, $conversions);
        }

        return $conversions;
    }

    /**
     * Make a request to the exchange rates API to get the exchange rates between a
     * date range. If only one currency is being used, we flatten the array to
     * remove currency symbol before returning it.
     *
     * @param  string  $from
     * @param  string|string[]  $to
     * @param  Carbon  $date
     * @param  Carbon  $endDate
     * @return array<string, float>|array<string, array<string, float>>
     *
     * @throws RequestException
     */
    private function makeRequestForExchangeRates(string $from, string|array $to, Carbon $date, Carbon $endDate): array
    {
        $symbols = is_string($to) ? $to : implode(',', $to);

        $result = $this->requestBuilder->makeRequest('/timeseries', [
            'base' => $from,
            'start_date' => $date->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'symbols' => $symbols,
        ]);

        $conversions = $result->rates();

        if (is_string($to)) {
            foreach ($conversions as $date => $rate) {
                $conversions[$date] = $rate[$to];
            }
        }

        ksort($conversions);

        return $conversions;
    }

    /**
     * Return the converted values between the $from and $to parameters. If no $date
     * parameter is passed, we use today's date instead.
     *
     * @param  int  $value
     * @param  string  $from
     * @param  string|string[]  $to
     * @param  Carbon|null  $date
     * @return float|array<string, float>
     *
     * @throws InvalidDateException
     * @throws InvalidCurrencyException
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function convert(int $value, string $from, string|array $to, Carbon $date = null): float|array
    {
        return $this->convertUsingRates(
            $this->exchangeRate($from, $to, $date),
            $to,
            $value,
        );
    }

    /**
     * Return an array of the converted values between the given date range.
     *
     * @param  int  $value
     * @param  string  $from
     * @param  string|string[]  $to
     * @param  Carbon  $date
     * @param  Carbon  $endDate
     * @return array<string, float>|array<string, array<string, float>>
     *
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function convertBetweenDateRange(
        int $value,
        string $from,
        string|array $to,
        Carbon $date,
        Carbon $endDate,
    ): array {
        return $this->convertUsingRatesForDateRange(
            $this->exchangeRateBetweenDateRange($from, $to, $date, $endDate),
            $to,
            $value,
        );
    }

    /**
     * If the 'from' and 'to' currencies are the same, we don't need to make a request to
     * the API. Instead, we can build the response ourselves to improve the performance.
     *
     * @param  Carbon  $startDate
     * @param  Carbon  $endDate
     * @return array<string, float>
     */
    public function exchangeRateDateRangeResultWithSameCurrency(
        Carbon $startDate,
        Carbon $endDate,
    ): array {
        $conversions = [];

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $conversions[$date->format('Y-m-d')] = 1.0;
            }
        }

        return $conversions;
    }

    /**
     * Determine whether if the exchange rate should be cached after it is fetched
     * from the API.
     *
     * @param  bool  $shouldCache
     * @return $this
     */
    public function shouldCache(bool $shouldCache = true): self
    {
        $this->shouldCache = $shouldCache;

        return $this;
    }

    /**
     * Determine whether if the cached result (if it exists) should be deleted. This
     * will force a new exchange rate to be fetched from the API.
     *
     * @param  bool  $bustCache
     * @return $this
     */
    public function shouldBustCache(bool $bustCache = true): self
    {
        $this->shouldBustCache = $bustCache;

        return $this;
    }

    /**
     * Attempt to fetch an item (more than likely an exchange rate) from the cache.
     * If it exists, return it. If it has been specified, bust the cache.
     *
     * @param  string  $cacheKey
     * @return mixed|null
     *
     * @throws InvalidArgumentException
     */
    public function attemptToResolveFromCache(string $cacheKey): mixed
    {
        if ($this->shouldBustCache) {
            $this->cacheRepository->forget($cacheKey);
            $this->shouldBustCache = false;
        } elseif ($cachedValue = $this->cacheRepository->getFromCache($cacheKey)) {
            return $cachedValue;
        }

        return null;
    }

    public function attemptToStoreInCache(string $cacheKey, mixed $currencies): void
    {
        if ($this->shouldCache) {
            $this->cacheRepository->storeInCache($cacheKey, $currencies);
        }
    }

    public function getRequestBuilder(): RequestSender
    {
        return $this->requestBuilder;
    }

    /**
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     */
    public function validateExchangeRateInput(string $from, array|string $to, ?Carbon $date): void
    {
        if ($date) {
            Validation::validateDate($date);
        }

        Validation::validateCurrencyCode($from);

        is_string($to) ? Validation::validateCurrencyCode($to) : Validation::validateCurrencyCodes($to);
    }

    /**
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     */
    public function validateExchangeRateBetweenDateRangeInput(string $from, array|string $to, Carbon $date, Carbon $endDate): void
    {
        Validation::validateCurrencyCode($from);
        Validation::validateStartAndEndDates($date, $endDate);

        is_string($to) ? Validation::validateCurrencyCode($to) : Validation::validateCurrencyCodes($to);
    }

    /**
     * Use the exchange rates we've just retrieved and convert the given value.
     *
     * @param  float|array<string,float>  $exchangeRates
     * @param  string|string[]  $to
     * @param  int  $value
     * @return float|array<string,float>
     */
    public function convertUsingRates(float|array $exchangeRates, string|array $to, int $value): float|array
    {
        if (is_string($to)) {
            return (float) $exchangeRates * $value;
        }

        foreach ($exchangeRates as $currency => $exchangeRate) {
            $exchangeRates[$currency] = (float) $exchangeRate * $value;
        }

        return $exchangeRates;
    }

    /**
     * Use the exchange rates we've just retrieved and convert the given value
     * for each date in the date range.
     *
     * @param  array<string,float|array<string,float>>  $exchangeRates
     * @param  string|string[]  $to
     * @param  int  $value
     * @return array<string,float|array<string,float>>
     */
    public function convertUsingRatesForDateRange(array $exchangeRates, string|array $to, int $value): array
    {
        $conversions = [];

        if (is_array($to)) {
            foreach ($exchangeRates as $date => $exchangeRate) {
                foreach ($exchangeRate as $currency => $rate) {
                    $conversions[$date][$currency] = (float) $rate * $value;
                }
            }

            return $conversions;
        }

        foreach ($exchangeRates as $date => $exchangeRate) {
            $conversions[$date] = (float) $exchangeRate * $value;
        }

        return $conversions;
    }
}

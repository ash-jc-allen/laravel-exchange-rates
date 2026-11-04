<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday;

use AshAllenDesign\LaravelExchangeRates\Classes\CacheRepository;
use AshAllenDesign\LaravelExchangeRates\Drivers\Support\SharedDriverLogicHandler;
use AshAllenDesign\LaravelExchangeRates\Exceptions\ExchangeRateException;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;

/**
 * Without an API key, this driver serves the latest European Central Bank
 * reference rates. With a key, it serves more currencies and, on plans
 * that include them, historical rates.
 *
 * @see https://allratestoday.com
 */
class AllRatesTodayDriver implements ExchangeRateDriver
{
    private RequestBuilder $requestBuilder;

    private CacheRepository $cacheRepository;

    private SharedDriverLogicHandler $sharedDriverLogicHandler;

    public function __construct(?RequestBuilder $requestBuilder = null, ?CacheRepository $cacheRepository = null)
    {
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder();
        $this->cacheRepository = $cacheRepository ?? new CacheRepository();

        $this->sharedDriverLogicHandler = new SharedDriverLogicHandler(
            $this->requestBuilder,
            $this->cacheRepository,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function currencies(): array
    {
        $cacheKey = 'currencies';

        if ($cachedExchangeRate = $this->sharedDriverLogicHandler->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $currencies = $this->requestBuilder->hasApiKey()
            ? array_column($this->requestBuilder->makeRequest('/v1/symbols')->get('currencies'), 'code')
            : array_keys($this->requestBuilder->makeRequest('/open/latest/USD')->rates());

        $this->sharedDriverLogicHandler->attemptToStoreInCache($cacheKey, $currencies);

        return $currencies;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ExchangeRateException
     */
    public function exchangeRate(string $from, array|string $to, ?Carbon $date = null): float|array
    {
        $this->sharedDriverLogicHandler->validateExchangeRateInput($from, $to, $date);

        if ($from === $to) {
            return 1.0;
        }

        if ($date) {
            $this->ensureApiKeyIsSet();
        }

        $cacheKey = $this->cacheRepository->buildCacheKey($from, $to, $date ?? Carbon::now());

        if ($cachedExchangeRate = $this->sharedDriverLogicHandler->attemptToResolveFromCache($cacheKey)) {
            // If the exchange rate has been retrieved from the cache as a
            // string (e.g. "1.23"), then cast it to a float (e.g. 1.23).
            // If we have retrieved the rates for many currencies, it
            // will be an array of floats, so just return it.
            return is_string($cachedExchangeRate)
                ? (float) $cachedExchangeRate
                : $cachedExchangeRate;
        }

        $exchangeRate = $this->requestedRates($this->fetchRates($from, $to, $date), $from, $to);

        $this->sharedDriverLogicHandler->attemptToStoreInCache($cacheKey, $exchangeRate);

        return $exchangeRate;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ExchangeRateException
     */
    public function exchangeRateBetweenDateRange(
        string $from,
        array|string $to,
        Carbon $date,
        Carbon $endDate
    ): array {
        $this->sharedDriverLogicHandler->validateExchangeRateBetweenDateRangeInput($from, $to, $date, $endDate);

        $cacheKey = $this->cacheRepository->buildCacheKey($from, $to, $date, $endDate);

        if ($cachedExchangeRate = $this->sharedDriverLogicHandler->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        if ($from === $to) {
            $conversions = $this->sharedDriverLogicHandler->exchangeRateDateRangeResultWithSameCurrency($date, $endDate);
        } else {
            $this->ensureApiKeyIsSet();

            $conversions = $this->makeRequestForExchangeRates($from, $to, $date, $endDate);
        }

        $this->sharedDriverLogicHandler->attemptToStoreInCache($cacheKey, $conversions);

        return $conversions;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ExchangeRateException
     */
    public function convert(int $value, string $from, array|string $to, ?Carbon $date = null): float|array
    {
        return $this->sharedDriverLogicHandler->convertUsingRates(
            $this->exchangeRate($from, $to, $date),
            $to,
            $value,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws ExchangeRateException
     */
    public function convertBetweenDateRange(
        int $value,
        string $from,
        array|string $to,
        Carbon $date,
        Carbon $endDate
    ): array {
        return $this->sharedDriverLogicHandler->convertUsingRatesForDateRange(
            $this->exchangeRateBetweenDateRange($from, $to, $date, $endDate),
            $to,
            $value,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function shouldCache(bool $shouldCache = true): ExchangeRateDriver
    {
        $this->sharedDriverLogicHandler->shouldCache($shouldCache);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldBustCache(bool $bustCache = true): ExchangeRateDriver
    {
        $this->sharedDriverLogicHandler->shouldBustCache($bustCache);

        return $this;
    }

    /**
     * @throws ExchangeRateException
     */
    private function ensureApiKeyIsSet(): void
    {
        if (! $this->requestBuilder->hasApiKey()) {
            throw new ExchangeRateException(
                'Historical exchange rates need an API key. Set the laravel-exchange-rates.api_key config value.'
            );
        }
    }

    /**
     * @param  string|string[]  $to
     * @return array<string, float>
     *
     * @throws RequestException
     */
    private function fetchRates(string $from, array|string $to, ?Carbon $date): array
    {
        if (! $this->requestBuilder->hasApiKey()) {
            return $this->requestBuilder->makeRequest('/open/latest/'.$from)->rates();
        }

        $queryParams = ['source' => $from, 'target' => is_string($to) ? $to : implode(',', $to)];

        if ($date) {
            $queryParams['time'] = $date->format('Y-m-d');
        }

        return $this->requestBuilder->makeRequest('/v1/rates', $queryParams)->rates();
    }

    /**
     * Pick the requested currencies, because the keyless endpoint answers with
     * its whole table.
     *
     * @param  array<string, float>  $rates
     * @param  string|string[]  $to
     * @return float|array<string, float>
     *
     * @throws ExchangeRateException
     */
    private function requestedRates(array $rates, string $from, array|string $to): float|array
    {
        $requested = array_intersect_key($rates, array_flip((array) $to));

        if ($missing = array_diff((array) $to, array_keys($requested))) {
            throw new ExchangeRateException(
                'No exchange rate from '.$from.' to '.implode(', ', $missing).'.'
                .($this->requestBuilder->hasApiKey() ? '' : ' An API key gives access to more currencies.')
            );
        }

        return is_string($to) ? (float) $requested[$to] : $requested;
    }

    /**
     * The API returns a daily series for one target at a time, so make one
     * request per target. If only one currency is being used, the dates map
     * straight to its rates.
     *
     * @param  string|string[]  $to
     * @return array<string, float>|array<string, array<string, float>>
     *
     * @throws RequestException
     */
    private function makeRequestForExchangeRates(string $from, string|array $to, Carbon $date, Carbon $endDate): array
    {
        $conversions = [];

        foreach ((array) $to as $target) {
            /** @var Response $result */
            $result = $this->requestBuilder->makeRequest('/v1/rates', [
                'source' => $from,
                'target' => $target,
                'from' => $date->format('Y-m-d'),
                'to' => $endDate->format('Y-m-d'),
                'group' => 'day',
            ]);

            foreach ($result->timeSeries() as $rateDate => $rate) {
                if (is_string($to)) {
                    $conversions[$rateDate] = $rate;
                } else {
                    $conversions[$rateDate][$target] = $rate;
                }
            }
        }

        ksort($conversions);

        return $conversions;
    }
}

<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Container\BindingResolutionException;

class ExchangeRate
{
    /**
     * The object used for making requests to the currency
     * conversion API.
     *
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var CacheRepository
     */
    private $cacheRepository;

    /**
     * @var bool
     */
    private $shouldBustCache = false;

    /**
     * ExchangeRate constructor.
     *
     * @param  RequestBuilder|null  $requestBuilder
     * @param  CacheRepository|null  $cacheRepository
     * @throws BindingResolutionException
     */
    public function __construct(RequestBuilder $requestBuilder = null, CacheRepository $cacheRepository = null)
    {
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder(new Client());
        $this->cacheRepository = $cacheRepository ?? new CacheRepository();
    }

    /**
     * Return an array of available currencies that
     * can be used with this package.
     *
     * @param  array  $currencies
     *
     * @return array
     */
    public function currencies(array $currencies = []): array
    {
        $cacheKey = 'currencies';

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $response = $this->requestBuilder->makeRequest('/latest', []);

        $currencies[] = $response['base'];

        foreach ($response['rates'] as $currency => $rate) {
            $currencies[] = $currency;
        }

        $this->cacheRepository->storeInCache($cacheKey, $currencies);

        return $currencies;
    }

    /**
     * Return the exchange rate between the $from and $to
     * parameters. If no $date parameter is passed, we
     * use today's date instead.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  Carbon|null  $date
     *
     * @return string
     * @throws InvalidDateException
     *
     * @throws InvalidCurrencyException
     */
    public function exchangeRate(string $from, string $to, ?Carbon $date = null): string
    {
        Validation::validateCurrencyCode($from);
        Validation::validateCurrencyCode($to);

        if ($date) {
            Validation::validateDate($date);
        }

        if ($from === $to) {
            return 1.0;
        }

        $cacheKey = $this->cacheRepository->buildCacheKey($from, $to, $date ?? now());

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        if ($date) {
            $exchangeRate = $this->requestBuilder->makeRequest('/'.$date->format('Y-m-d'),
                ['base' => $from])['rates'][$to];
        } else {
            $exchangeRate = $this->requestBuilder->makeRequest('/latest', ['base' => $from])['rates'][$to];
        }

        $this->cacheRepository->storeInCache($cacheKey, $exchangeRate);

        return $exchangeRate;
    }

    /**
     * Return the exchange rates between the given
     * date range.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  Carbon  $date
     * @param  Carbon  $endDate
     * @param  array  $conversions
     *
     * @return array
     * @throws Exception
     */
    public function exchangeRateBetweenDateRange(
        string $from,
        string $to,
        Carbon $date,
        Carbon $endDate,
        array $conversions = []
    ) {
        Validation::validateCurrencyCode($from);
        Validation::validateCurrencyCode($to);
        Validation::validateStartAndEndDates($date, $endDate);

        if ($from === $to) {
            for ($startDate = $date; $startDate->lte($endDate); $startDate->addDay()) {
                if ($date->isWeekday()) {
                    $conversions[$date->format('Y-m-d')] = 1.0;
                }
            }

            return $conversions;
        }

        $cacheKey = $this->cacheRepository->buildCacheKey($from, $to, $date, $endDate);

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $result = $this->requestBuilder->makeRequest('/history', [
            'base'     => $from,
            'start_at' => $date->format('Y-m-d'),
            'end_at'   => $endDate->format('Y-m-d'),
            'symbols'  => $to,
        ]);

        foreach ($result['rates'] as $date => $rate) {
            $conversions[$date] = $rate[$to];
        }

        ksort($conversions);

        $this->cacheRepository->storeInCache($cacheKey, $conversions);

        return $conversions;
    }

    /**
     * Return the converted values between the $from and $to
     * parameters. If no $date parameter is passed, we
     * use today's date instead.
     *
     * @param  int  $value
     * @param  string  $from
     * @param  string  $to
     * @param  Carbon|null  $date
     *
     * @return float
     * @throws InvalidDateException
     *
     * @throws InvalidCurrencyException
     */
    public function convert(int $value, string $from, string $to, Carbon $date = null): float
    {
        return (float) $this->exchangeRate($from, $to, $date) * $value;
    }

    /**
     * Return an array of the converted values between
     * the given date range.
     *
     * @param  int  $value
     * @param  string  $from
     * @param  string  $to
     * @param  Carbon  $date
     * @param  Carbon  $endDate
     * @param  array  $conversions
     *
     * @return array
     * @throws Exception
     */
    public function convertBetweenDateRange(
        int $value,
        string $from,
        string $to,
        Carbon $date,
        Carbon $endDate,
        array $conversions = []
    ): array {
        foreach ($this->exchangeRateBetweenDateRange($from, $to, $date, $endDate) as $date => $exchangeRate) {
            $conversions[$date] = (float) $exchangeRate * $value;
        }

        ksort($conversions);

        return $conversions;
    }

    public function shouldBustCache(bool $bustCache = true): self
    {
        $this->shouldBustCache = $bustCache;

        return $this;
    }

    private function attemptToResolveFromCache(string $cacheKey)
    {
        if ($this->shouldBustCache) {
            $this->cacheRepository->forget($cacheKey);
            $this->shouldBustCache = false;
        } elseif ($cachedValue = $this->cacheRepository->getFromCache($cacheKey)) {
            return $cachedValue;
        }
    }
}

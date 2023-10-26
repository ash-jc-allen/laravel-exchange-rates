<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost;

use AshAllenDesign\LaravelExchangeRates\Classes\CacheRepository;
use AshAllenDesign\LaravelExchangeRates\Drivers\Support\SharedDriverLogicHandler;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Carbon\Carbon;

/**
 * @see https://exchangerate.host
 */
class ExchangeRateHostDriver implements ExchangeRateDriver
{
    private SharedDriverLogicHandler $sharedDriverLogicHandler;

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
     * @inheritDoc
     */
    public function currencies(): array
    {
        $cacheKey = 'currencies';

        if ($cachedExchangeRate = $this->sharedDriverLogicHandler->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $response = $this->sharedDriverLogicHandler
            ->getRequestBuilder()
            ->makeRequest('/list');

        $currencies = array_keys($response->get('currencies'));

        // TODO Update all drivers to use this approach.
        $this->sharedDriverLogicHandler->attemptToStoreInCache($cacheKey, $currencies);

        return $currencies;
    }

    /**
     * @inheritDoc
     */
    public function exchangeRate(string $from, array|string $to, Carbon $date = null): float|array
    {
        return $this->sharedDriverLogicHandler->exchangeRate($from, $to, $date);
    }

    /**
     * @inheritDoc
     */
    public function exchangeRateBetweenDateRange(
        string $from,
        array|string $to,
        Carbon $date,
        Carbon $endDate
    ): array {
        return $this->sharedDriverLogicHandler->exchangeRateBetweenDateRange($from, $to, $date, $endDate);
    }

    /**
     * @inheritDoc
     */
    public function convert(int $value, string $from, array|string $to, Carbon $date = null): float|array
    {
        return $this->sharedDriverLogicHandler->convert($value, $from, $to, $date);
    }

    /**
     * @inheritDoc
     */
    public function convertBetweenDateRange(
        int $value,
        string $from,
        array|string $to,
        Carbon $date,
        Carbon $endDate
    ): array {
        return $this->sharedDriverLogicHandler->convertBetweenDateRange($value, $from, $to, $date, $endDate);
    }

    /**
     * @inheritDoc
     */
    public function shouldCache(bool $shouldCache = true): ExchangeRateDriver
    {
        $this->sharedDriverLogicHandler->shouldCache($shouldCache);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function shouldBustCache(bool $bustCache = true): ExchangeRateDriver
    {
        $this->sharedDriverLogicHandler->shouldBustCache($bustCache);

        return $this;
    }
}

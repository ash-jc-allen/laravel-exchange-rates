<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo;

use AshAllenDesign\LaravelExchangeRates\Classes\CacheRepository;
use AshAllenDesign\LaravelExchangeRates\Drivers\Support\SharedDriverLogicHandler;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Carbon\Carbon;

/**
 * @see https://exchangeratesapi.io/
 */
class ExchangeRatesApiIoDriver implements ExchangeRateDriver
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
    public function currencies(array $currencies = []): array
    {
        return $this->sharedDriverLogicHandler->currencies($currencies);
    }

    /**
     * @inheritDoc
     */
    public function exchangeRate(string $from, $to, Carbon $date = null)
    {
        return $this->sharedDriverLogicHandler->exchangeRate($from, $to, $date);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function convert(int $value, string $from, $to, Carbon $date = null)
    {
        return $this->sharedDriverLogicHandler->convert($value, $from, $to, $date);
    }

    /**
     * @inheritDoc
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

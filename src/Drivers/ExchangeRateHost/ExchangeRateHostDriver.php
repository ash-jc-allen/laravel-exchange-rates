<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost;

use AshAllenDesign\LaravelExchangeRates\Drivers\Support\Driver;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;

/**
 * @see https://exchangerate.host
 */
class ExchangeRateHostDriver extends Driver
{
    public array $driverRequestBuilder = [RequestBuilder::class];

    /**
     * @inheritDoc
     */
    public function currencies(): array
    {
        $cacheKey = 'currencies';

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $response = $this->getRequestBuilder()
            ->makeRequest('/list');

        $currencies = array_keys($response->get('currencies'));

        $this->attemptToStoreInCache($cacheKey, $currencies);

        return $currencies;
    }

    /**
     * @inheritDoc
     */
    public function exchangeRate(string $from, array|string $to, Carbon $date = null): float|array
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
        $queryParams = ['source' => $from, 'currencies' => $symbols];

        if ($date) {
            $queryParams['date'] = $date->format('Y-m-d');
        }

        $url = $date ? '/historical' : '/live';

        /** @var array<string,float> $response */
        $response = $this->getRequestBuilder()
            ->makeRequest($url, $queryParams)
            ->rates();

        $exchangeRate = is_string($to)
            ? $response[$from.$to]
            : $this->removeSourceCurrencyFromKeys($response);

        $this->attemptToStoreInCache($cacheKey, $exchangeRate);

        return $exchangeRate;
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
        $this->validateExchangeRateBetweenDateRangeInput($from, $to, $date, $endDate);

        $cacheKey = $this->cacheRepository->buildCacheKey($from, $to, $date, $endDate);

        if ($cachedExchangeRate = $this->attemptToResolveFromCache($cacheKey)) {
            return $cachedExchangeRate;
        }

        $conversions = $from === $to
            ? $this->exchangeRateDateRangeResultWithSameCurrency($date, $endDate)
            : $this->makeRequestForExchangeRates($from, $to, $date, $endDate);

        $this->attemptToStoreInCache($cacheKey, $conversions);

        return $conversions;
    }

    /**
     * @inheritDoc
     */
    public function convert(int $value, string $from, array|string $to, Carbon $date = null): float|array
    {
        return $this->convertUsingRates(
            $this->exchangeRate($from, $to, $date),
            $to,
            $value,
        );
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
        return $this->convertUsingRatesForDateRange(
            $this->exchangeRateBetweenDateRange($from, $to, $date, $endDate),
            $to,
            $value,
        );
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

        $result = $this->getRequestBuilder()
            ->makeRequest('/timeframe', [
                'source' => $from,
                'start_date' => $date->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'currencies' => $symbols,
            ]);

        $conversions = $result->rates();

        foreach ($conversions as $rateDate => $rates) {
            $ratesForDay = is_string($to)
                ? $rates[$from.$to]
                : $this->removeSourceCurrencyFromKeys($rates);

            $conversions[$rateDate] = $ratesForDay;
        }

        ksort($conversions);

        return $conversions;
    }

    /**
     * The quotes are returned in the format of "USDEUR": 0.1234. We only want the
     * converted currency's code (e.g. EUR), so we need to remove the source
     * currency from the start of the key (e.g. USD). We can do this by
     * removing the first three characters from the key. Strip these
     * characters from all the rates and then return the array.
     *
     * @param  array<string,float>  $rates
     * @return array<string,float>
     */
    private function removeSourceCurrencyFromKeys(array $rates): array
    {
        return collect($rates)
            ->mapWithKeys(static fn (float $value, string $key): array => [substr($key, 3) => $value])
            ->all();
    }
}

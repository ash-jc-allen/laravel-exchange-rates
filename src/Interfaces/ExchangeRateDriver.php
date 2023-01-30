<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Interfaces;

use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Psr\SimpleCache\InvalidArgumentException;

interface ExchangeRateDriver
{
    /**
     * Return an array of available currencies that can be used with this package.
     *
     * @return string[]
     *
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function currencies(): array;

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
    public function exchangeRate(string $from, array|string $to, Carbon $date = null): float|array;

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
        array|string $to,
        Carbon $date,
        Carbon $endDate
    ): array;

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
    public function convert(int $value, string $from, array|string $to, Carbon $date = null): float|array;

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
        array|string $to,
        Carbon $date,
        Carbon $endDate
    ): array;

    /**
     * Determine whether if the exchange rate should be cached after it is fetched
     * from the API.
     *
     * @param  bool  $shouldCache
     * @return $this
     */
    public function shouldCache(bool $shouldCache = true): self;

    /**
     * Determine whether if the cached result (if it exists) should be deleted. This
     * will force a new exchange rate to be fetched from the API.
     *
     * @param  bool  $bustCache
     * @return $this
     */
    public function shouldBustCache(bool $bustCache = true): self;
}

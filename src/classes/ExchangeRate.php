<?php

namespace AshAllenDesign\LaravelExchangeRates;

use AshAllenDesign\LaravelExchangeRates\classes\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\classes\Validation;
use AshAllenDesign\LaravelExchangeRates\exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\exceptions\InvalidDateException;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;

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
     * ExchangeRate constructor.
     *
     * @param RequestBuilder|null $requestBuilder
     */
    public function __construct(RequestBuilder $requestBuilder = null)
    {
        $this->requestBuilder = $requestBuilder ?? (new RequestBuilder(new Client()));
    }

    /**
     * Return an array of available currencies that
     * can be used with this package.
     *
     * @param array $currencies
     *
     * @return array
     */
    public function currencies(array $currencies = []): array
    {
        $response = $this->requestBuilder->makeRequest('/latest', []);

        $currencies[] = $response['base'];

        foreach ($response['rates'] as $currency => $rate) {
            $currencies[] = $currency;
        }

        return $currencies;
    }

    /**
     * Return the exchange rate between the $from and $to
     * parameters. If no $date parameter is passed, we
     * use today's date instead.
     *
     * @param string      $from
     * @param string      $to
     * @param Carbon|null $date
     *
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     *
     * @return string
     */
    public function exchangeRate(string $from, string $to, Carbon $date = null): string
    {
        Validation::validateCurrencyCode($from);
        Validation::validateCurrencyCode($to);

        if ($date) {
            Validation::validateDate($date);

            return $this->requestBuilder->makeRequest('/'.$date->format('Y-m-d'), ['base' => $from])['rates'][$to];
        }

        return $this->requestBuilder->makeRequest('/latest', ['base' => $from])['rates'][$to];
    }

    /**
     * Return the exchange rates between the given
     * date range.
     *
     * @param string $from
     * @param string $to
     * @param Carbon $date
     * @param Carbon $endDate
     * @param array  $conversions
     *
     * @throws Exception
     *
     * @return array
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

        return $conversions;
    }

    /**
     * Return the converted values between the $from and $to
     * parameters. If no $date parameter is passed, we
     * use today's date instead.
     *
     * @param int         $value
     * @param string      $from
     * @param string      $to
     * @param Carbon|null $date
     *
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     *
     * @return float
     */
    public function convert(int $value, string $from, string $to, Carbon $date = null): float
    {
        return (float) $this->exchangeRate($from, $to, $date) * $value;
    }

    /**
     * Return an array of the converted values between
     * the given date range.
     *
     * @param int    $value
     * @param string $from
     * @param string $to
     * @param Carbon $date
     * @param Carbon $endDate
     * @param array  $conversions
     *
     * @throws Exception
     *
     * @return array
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
}

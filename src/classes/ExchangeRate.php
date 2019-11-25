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
     * @param array $currencies
     *
     * @return array
     */
    public function currencies(array $currencies = [])
    {
        $response = $this->requestBuilder->makeRequest('/latest', []);

        $currencies[] = $response['base'];

        foreach ($response['rates'] as $currency => $rate) {
            $currencies[] = $currency;
        }

        return $currencies;
    }

    /**
     * @param string      $from
     * @param string      $to
     * @param Carbon|null $date
     *
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     *
     * @return mixed
     */
    public function exchangeRate(string $from, string $to, Carbon $date = null)
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
     * @param string $from
     * @param string $to
     * @param Carbon $date
     * @param Carbon $endDate
     * @param array  $conversions
     *
     * @throws Exception
     *
     * @return mixed
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
    ) {
        foreach ($this->exchangeRateBetweenDateRange($from, $to, $date, $endDate) as $date => $exchangeRate) {
            $conversions[$date] = (float) $exchangeRate * $value;
        }

        ksort($conversions);

        return $conversions;
    }
}

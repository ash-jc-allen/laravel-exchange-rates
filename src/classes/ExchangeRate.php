<?php

namespace AshAllenDesign\LaravelExchangeRates;

use AshAllenDesign\LaravelExchangeRates\classes\Currencies;
use AshAllenDesign\LaravelExchangeRates\classes\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\exceptions\InvalidDateException;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

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
     * @param string $from
     * @param string $to
     * @param Carbon|null $date
     *
     * @return mixed
     * @throws InvalidCurrencyException
     * @throws InvalidDateException
     */
    public function exchangeRate(string $from, string $to, Carbon $date = null)
    {
        $this->validateCurrencyCode($from);
        $this->validateCurrencyCode($to);

        if ($date) {
            $this->validateDate($date);
            return $this->requestBuilder->makeRequest('/' . $date->format('Y-m-d'), ['base' => $from])['rates'][$to];
        }

        return $this->requestBuilder->makeRequest('/latest', ['base' => $from])['rates'][$to];
    }

    /**
     * @param string $from
     * @param string $to
     * @param Carbon $date
     * @param Carbon $endDate
     * @param array $conversions
     *
     * @return mixed
     * @throws Exception
     *
     */
    public function exchangeRateBetweenDateRange(
        string $from,
        string $to,
        Carbon $date,
        Carbon $endDate,
        array $conversions = []
    ) {
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
     * @param int $value
     * @param string $from
     * @param string $to
     * @param Carbon|null $date
     *
     * @return float|int
     * @throws InvalidCurrencyException
     */
    public function convert(int $value, string $from, string $to, Carbon $date = null)
    {
        $result = Money::{$to}($value)->multiply($this->exchangeRate($from, $to, $date));

        return (new DecimalMoneyFormatter(new IsoCurrencies()))->format($result);
    }

    /**
     * @param int $value
     * @param string $from
     * @param string $to
     * @param Carbon $date
     * @param Carbon $endDate
     * @param array $conversions
     *
     * @return array
     * @throws Exception
     *
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
            $result = Money::{$from}($value)->multiply($exchangeRate);
            $conversions[$date] = (float)(new DecimalMoneyFormatter(new IsoCurrencies()))->format($result);
        }

        ksort($conversions);

        return $conversions;
    }

    /**
     * @param string $currencyCode
     * @throws InvalidCurrencyException
     */
    private function validateCurrencyCode(string $currencyCode)
    {
        $currencies = new Currencies();

        if (!$currencies->isAllowableCurrency($currencyCode)) {
            throw new InvalidCurrencyException($currencyCode . ' is not a valid country code.');
        }
    }

    /**
     * Validate that both of the dates are in the
     * past. After this, check that the 'from'
     * date is not after the 'to' date.
     *
     * @param Carbon $from
     * @param Carbon $to
     * @throws InvalidDateException
     */
    private function validateFromAndToDates(Carbon $from, Carbon $to)
    {
        $this->validateDate($from);
        $this->validateDate($to);

        if ($from->isAfter($to)) {
            throw new InvalidDateException('The \'from\' date must be before the \'to\' date.');
        }
    }

    /**
     * Validate the date that has been passed.
     * We check that the date is in the past.
     *
     * @param Carbon $date
     * @throws InvalidDateException
     */
    private function validateDate(Carbon $date)
    {
        if (!$date->isPast()) {
            throw new InvalidDateException('The date must be in the past.');
        }
    }
}

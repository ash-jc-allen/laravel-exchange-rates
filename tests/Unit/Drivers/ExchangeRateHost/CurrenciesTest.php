<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\ExchangeRateHost;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost\ExchangeRateHostDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost\Response;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Cache;
use Mockery;

final class CurrenciesTest extends TestCase
{
    /** @test */
    public function currencies_are_returned_as_an_array_if_no_currencies_are_cached(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/list'])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new ExchangeRateHostDriver($requestBuilderMock);
        $currencies = $exchangeRate->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);

        $this->assertNotNull(Cache::get('laravel_xr_currencies'));
    }

    /** @test */
    public function cached_currencies_are_returned_if_they_are_in_the_cache(): void
    {
        Cache::forever('laravel_xr_currencies', ['CUR1', 'CUR2', 'CUR3']);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')->never();

        $exchangeRate = new ExchangeRateHostDriver($requestBuilderMock);
        $currencies = $exchangeRate->currencies();

        $this->assertEquals(['CUR1', 'CUR2', 'CUR3'], $currencies);
    }

    /** @test */
    public function currencies_are_fetched_if_the_currencies_are_cached_but_the_should_bust_cache_method_called(): void
    {
        Cache::forever('currencies', ['CUR1', 'CUR2', 'CUR3']);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/list'])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new ExchangeRateHostDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldBustCache()->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);
    }

    /** @test */
    public function currencies_are_not_cached_if_the_shouldCache_option_is_false(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/list'])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new ExchangeRateHostDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldCache(false)->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);

        $this->assertNull(Cache::get('laravel_xr_currencies'));
    }

    private function mockResponse(): Response
    {
        return new Response([
            'success' => true,
            'terms' => 'https://currencylayer.com/terms',
            'privacy' => 'https://currencylayer.com/privacy',
            'currencies' => [
                'AED' => 'United Arab Emirates Dirham',
                'AFN' => 'Afghan Afghani',
                'ALL' => 'Albanian Lek',
                'AMD' => 'Armenian Dram',
                'ANG' => 'Netherlands Antillean Guilder',
                'AOA' => 'Angolan Kwanza',
                'ARS' => 'Argentine Peso',
                'AUD' => 'Australian Dollar',
                'AWG' => 'Aruban Florin',
                'AZN' => 'Azerbaijani Manat',
                'BAM' => 'Bosnia-Herzegovina Convertible Mark',
                'BBD' => 'Barbadian Dollar',
                'BDT' => 'Bangladeshi Taka',
                'BGN' => 'Bulgarian Lev',
                'BHD' => 'Bahraini Dinar',
                'BIF' => 'Burundian Franc',
                'BMD' => 'Bermudan Dollar',
                'BND' => 'Brunei Dollar',
                'BOB' => 'Bolivian Boliviano',
                'BRL' => 'Brazilian Real',
                'BSD' => 'Bahamian Dollar',
                'BTC' => 'Bitcoin',
                'BTN' => 'Bhutanese Ngultrum',
                'BWP' => 'Botswanan Pula',
                'BYN' => 'New Belarusian Ruble',
                'BYR' => 'Belarusian Ruble',
                'BZD' => 'Belize Dollar',
                'CAD' => 'Canadian Dollar',
                'CDF' => 'Congolese Franc',
                'CHF' => 'Swiss Franc',
                'CLF' => 'Chilean Unit of Account (UF)',
                'CLP' => 'Chilean Peso',
                'CNY' => 'Chinese Yuan',
                'COP' => 'Colombian Peso',
                'CRC' => 'Costa Rican Colón',
                'CUC' => 'Cuban Convertible Peso',
                'CUP' => 'Cuban Peso',
                'CVE' => 'Cape Verdean Escudo',
                'CZK' => 'Czech Republic Koruna',
                'DJF' => 'Djiboutian Franc',
                'DKK' => 'Danish Krone',
                'DOP' => 'Dominican Peso',
                'DZD' => 'Algerian Dinar',
                'EGP' => 'Egyptian Pound',
                'ERN' => 'Eritrean Nakfa',
                'ETB' => 'Ethiopian Birr',
                'EUR' => 'Euro',
                'FJD' => 'Fijian Dollar',
                'FKP' => 'Falkland Islands Pound',
                'GBP' => 'British Pound Sterling',
                'GEL' => 'Georgian Lari',
                'GGP' => 'Guernsey Pound',
                'GHS' => 'Ghanaian Cedi',
                'GIP' => 'Gibraltar Pound',
                'GMD' => 'Gambian Dalasi',
                'GNF' => 'Guinean Franc',
                'GTQ' => 'Guatemalan Quetzal',
                'GYD' => 'Guyanaese Dollar',
                'HKD' => 'Hong Kong Dollar',
                'HNL' => 'Honduran Lempira',
                'HRK' => 'Croatian Kuna',
                'HTG' => 'Haitian Gourde',
                'HUF' => 'Hungarian Forint',
                'IDR' => 'Indonesian Rupiah',
                'ILS' => 'Israeli New Sheqel',
                'IMP' => 'Manx pound',
                'INR' => 'Indian Rupee',
                'IQD' => 'Iraqi Dinar',
                'IRR' => 'Iranian Rial',
                'ISK' => 'Icelandic Króna',
                'JEP' => 'Jersey Pound',
                'JMD' => 'Jamaican Dollar',
                'JOD' => 'Jordanian Dinar',
                'JPY' => 'Japanese Yen',
                'KES' => 'Kenyan Shilling',
                'KGS' => 'Kyrgystani Som',
                'KHR' => 'Cambodian Riel',
                'KMF' => 'Comorian Franc',
                'KPW' => 'North Korean Won',
                'KRW' => 'South Korean Won',
                'KWD' => 'Kuwaiti Dinar',
                'KYD' => 'Cayman Islands Dollar',
                'KZT' => 'Kazakhstani Tenge',
                'LAK' => 'Laotian Kip',
                'LBP' => 'Lebanese Pound',
                'LKR' => 'Sri Lankan Rupee',
                'LRD' => 'Liberian Dollar',
                'LSL' => 'Lesotho Loti',
                'LTL' => 'Lithuanian Litas',
                'LVL' => 'Latvian Lats',
                'LYD' => 'Libyan Dinar',
                'MAD' => 'Moroccan Dirham',
                'MDL' => 'Moldovan Leu',
                'MGA' => 'Malagasy Ariary',
                'MKD' => 'Macedonian Denar',
                'MMK' => 'Myanma Kyat',
                'MNT' => 'Mongolian Tugrik',
                'MOP' => 'Macanese Pataca',
                'MRO' => 'Mauritanian Ouguiya',
                'MUR' => 'Mauritian Rupee',
                'MVR' => 'Maldivian Rufiyaa',
                'MWK' => 'Malawian Kwacha',
                'MXN' => 'Mexican Peso',
                'MYR' => 'Malaysian Ringgit',
                'MZN' => 'Mozambican Metical',
                'NAD' => 'Namibian Dollar',
                'NGN' => 'Nigerian Naira',
                'NIO' => 'Nicaraguan Córdoba',
                'NOK' => 'Norwegian Krone',
                'NPR' => 'Nepalese Rupee',
                'NZD' => 'New Zealand Dollar',
                'OMR' => 'Omani Rial',
                'PAB' => 'Panamanian Balboa',
                'PEN' => 'Peruvian Nuevo Sol',
                'PGK' => 'Papua New Guinean Kina',
                'PHP' => 'Philippine Peso',
                'PKR' => 'Pakistani Rupee',
                'PLN' => 'Polish Zloty',
                'PYG' => 'Paraguayan Guarani',
                'QAR' => 'Qatari Rial',
                'RON' => 'Romanian Leu',
                'RSD' => 'Serbian Dinar',
                'RUB' => 'Russian Ruble',
                'RWF' => 'Rwandan Franc',
                'SAR' => 'Saudi Riyal',
                'SBD' => 'Solomon Islands Dollar',
                'SCR' => 'Seychellois Rupee',
                'SDG' => 'South Sudanese Pound',
                'SEK' => 'Swedish Krona',
                'SGD' => 'Singapore Dollar',
                'SHP' => 'Saint Helena Pound',
                'SLE' => 'Sierra Leonean Leone',
                'SLL' => 'Sierra Leonean Leone',
                'SOS' => 'Somali Shilling',
                'SRD' => 'Surinamese Dollar',
                'STD' => 'São Tomé and Príncipe Dobra',
                'SVC' => 'Salvadoran Colón',
                'SYP' => 'Syrian Pound',
                'SZL' => 'Swazi Lilangeni',
                'THB' => 'Thai Baht',
                'TJS' => 'Tajikistani Somoni',
                'TMT' => 'Turkmenistani Manat',
                'TND' => 'Tunisian Dinar',
                'TOP' => 'Tongan Paʻanga',
                'TRY' => 'Turkish Lira',
                'TTD' => 'Trinidad and Tobago Dollar',
                'TWD' => 'New Taiwan Dollar',
                'TZS' => 'Tanzanian Shilling',
                'UAH' => 'Ukrainian Hryvnia',
                'UGX' => 'Ugandan Shilling',
                'USD' => 'United States Dollar',
                'UYU' => 'Uruguayan Peso',
                'UZS' => 'Uzbekistan Som',
                'VEF' => 'Venezuelan Bolívar Fuerte',
                'VES' => 'Sovereign Bolivar',
                'VND' => 'Vietnamese Dong',
                'VUV' => 'Vanuatu Vatu',
                'WST' => 'Samoan Tala',
                'XAF' => 'CFA Franc BEAC',
                'XAG' => 'Silver (troy ounce)',
                'XAU' => 'Gold (troy ounce)',
                'XCD' => 'East Caribbean Dollar',
                'XDR' => 'Special Drawing Rights',
                'XOF' => 'CFA Franc BCEAO',
                'XPF' => 'CFP Franc',
                'YER' => 'Yemeni Rial',
                'ZAR' => 'South African Rand',
                'ZMK' => 'Zambian Kwacha (pre-2013)',
                'ZMW' => 'Zambian Kwacha',
                'ZWL' => 'Zimbabwean Dollar',
            ],
        ]);
    }

    private function expectedResponse(): array
    {
        return [
            'AED',
            'AFN',
            'ALL',
            'AMD',
            'ANG',
            'AOA',
            'ARS',
            'AUD',
            'AWG',
            'AZN',
            'BAM',
            'BBD',
            'BDT',
            'BGN',
            'BHD',
            'BIF',
            'BMD',
            'BND',
            'BOB',
            'BRL',
            'BSD',
            'BTC',
            'BTN',
            'BWP',
            'BYN',
            'BYR',
            'BZD',
            'CAD',
            'CDF',
            'CHF',
            'CLF',
            'CLP',
            'CNY',
            'COP',
            'CRC',
            'CUC',
            'CUP',
            'CVE',
            'CZK',
            'DJF',
            'DKK',
            'DOP',
            'DZD',
            'EGP',
            'ERN',
            'ETB',
            'EUR',
            'FJD',
            'FKP',
            'GBP',
            'GEL',
            'GGP',
            'GHS',
            'GIP',
            'GMD',
            'GNF',
            'GTQ',
            'GYD',
            'HKD',
            'HNL',
            'HRK',
            'HTG',
            'HUF',
            'IDR',
            'ILS',
            'IMP',
            'INR',
            'IQD',
            'IRR',
            'ISK',
            'JEP',
            'JMD',
            'JOD',
            'JPY',
            'KES',
            'KGS',
            'KHR',
            'KMF',
            'KPW',
            'KRW',
            'KWD',
            'KYD',
            'KZT',
            'LAK',
            'LBP',
            'LKR',
            'LRD',
            'LSL',
            'LTL',
            'LVL',
            'LYD',
            'MAD',
            'MDL',
            'MGA',
            'MKD',
            'MMK',
            'MNT',
            'MOP',
            'MRO',
            'MUR',
            'MVR',
            'MWK',
            'MXN',
            'MYR',
            'MZN',
            'NAD',
            'NGN',
            'NIO',
            'NOK',
            'NPR',
            'NZD',
            'OMR',
            'PAB',
            'PEN',
            'PGK',
            'PHP',
            'PKR',
            'PLN',
            'PYG',
            'QAR',
            'RON',
            'RSD',
            'RUB',
            'RWF',
            'SAR',
            'SBD',
            'SCR',
            'SDG',
            'SEK',
            'SGD',
            'SHP',
            'SLE',
            'SLL',
            'SOS',
            'SRD',
            'STD',
            'SVC',
            'SYP',
            'SZL',
            'THB',
            'TJS',
            'TMT',
            'TND',
            'TOP',
            'TRY',
            'TTD',
            'TWD',
            'TZS',
            'UAH',
            'UGX',
            'USD',
            'UYU',
            'UZS',
            'VEF',
            'VES',
            'VND',
            'VUV',
            'WST',
            'XAF',
            'XAG',
            'XAU',
            'XCD',
            'XDR',
            'XOF',
            'XPF',
            'YER',
            'ZAR',
            'ZMK',
            'ZMW',
            'ZWL',
        ];
    }
}

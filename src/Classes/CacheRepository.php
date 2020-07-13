<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Exceptions\ExchangeRateException;
use Carbon\Carbon;
use Illuminate\Cache\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;

class CacheRepository
{
    /**
     * @var Repository
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cachePrefix = 'laravel_xr_';

    /**
     * Cache constructor.
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $cache = Container::getInstance()->make('cache');
        $config = Container::getInstance()->make('config')->get('cache.default');

        $this->cache = $cache->store($config);
    }

    /**
     * Forget the item from the cache.
     *
     * @param  string  $key
     * @return CacheRepository
     */
    public function forget(string $key): self
    {
        $this->cache->forget($this->cachePrefix.$key);

        return $this;
    }

    /**
     * Store a new item in the cache.
     *
     * @param  string  $key
     * @param $value
     * @return bool
     */
    public function storeInCache(string $key, $value): bool
    {
        return $this->cache->forever($this->cachePrefix.$key, $value);
    }

    /**
     * Get an item from the cache if it exists.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getFromCache(string $key)
    {
        return $this->cache->get($this->cachePrefix.$key);
    }

    /**
     * Determine whether if an item exists in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function existsInCache(string $key): bool
    {
        return $this->cache->has($this->cachePrefix.$key);
    }

    /**
     * Build the key that can be used for fetching or
     * storing items in the cache. We can pass a
     * fourth parameter if we are storing
     * exchange rates for a given date
     * range.
     *
     * @param  string  $from
     * @param  string|array  $to
     * @param  Carbon  $date
     * @param  Carbon|null  $endDate
     * @return string
     * @throws ExchangeRateException
     */
    public function buildCacheKey(string $from, $to, Carbon $date, Carbon $endDate = null): string
    {
        Validation::validateIsStringOrArray($to);

        if (is_array($to)) {
            asort($to);
            $to = implode('_', $to);
        }

        $key = $from.'_'.$to.'_'.$date->format('Y-m-d');

        if ($endDate) {
            $key .= '_'.$endDate->format('Y-m-d');
        }

        return $key;
    }
}

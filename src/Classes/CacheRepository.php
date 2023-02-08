<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\SimpleCache\InvalidArgumentException;

class CacheRepository
{
    protected Repository $cache;

    protected string $cachePrefix = 'laravel_xr_';

    /**
     * Cache constructor.
     *
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
     * @param  string|array  $value
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
     *
     * @throws InvalidArgumentException
     */
    public function getFromCache(string $key)
    {
        return $this->cache->get($this->cachePrefix.$key);
    }

    /**
     * Build the key that can be used for fetching or
     * storing items in the cache. We can pass a
     * fourth parameter if we are storing
     * exchange rates for a given date
     * range.
     *
     * @param  string  $from
     * @param  string|string[]  $to
     * @param  Carbon  $date
     * @param  Carbon|null  $endDate
     * @return string
     */
    public function buildCacheKey(string $from, string|array $to, Carbon $date, Carbon $endDate = null): string
    {
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

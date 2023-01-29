<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Concerns;

trait InteractsWithCache
{
    /**
     * Determine whether if the exchange rate should be
     * cached after it is fetched from the API.
     *
     * @param  bool  $shouldCache
     * @return $this
     */
    public function shouldCache(bool $shouldCache = true): self
    {
        $this->shouldCache = $shouldCache;

        return $this;
    }

    /**
     * Determine whether if the cached result (if it
     * exists) should be deleted. This will force
     * a new exchange rate to be fetched from
     * the API.
     *
     * @param  bool  $bustCache
     * @return $this
     */
    public function shouldBustCache(bool $bustCache = true): self
    {
        $this->shouldBustCache = $bustCache;

        return $this;
    }

    /**
     * Attempt to fetch an item (more than likely an
     * exchange rate) from the cache. If it exists,
     * return it. If it has been specified, bust
     * the cache.
     *
     * @param  string  $cacheKey
     * @return mixed
     */
    private function attemptToResolveFromCache(string $cacheKey)
    {
        if ($this->shouldBustCache) {
            $this->cacheRepository->forget($cacheKey);
            $this->shouldBustCache = false;
        } elseif ($cachedValue = $this->cacheRepository->getFromCache($cacheKey)) {
            return $cachedValue;
        }
    }
}

<?php

namespace Tokenly\CryptoQuoteClient\Transport\Concerns;

use Illuminate\Support\Facades\Cache;

/**
 * Caches quotes
 */
trait CachesAggregateTickerResponse
{

    public function getAggregateTickerResponseWithCache(callable $buildAggregateTickerResponse_fn)
    {
        if (class_exists(Cache::class)) {
            $key = class_basename($this) . '.ticker';
            $CACHE_MINUTES = 1;

            $ticker_response = Cache::remember($key, $CACHE_MINUTES, function () {
                return $buildAggregateTickerResponse_fn();
            });
        } else {
            $ticker_response = $buildAggregateTickerResponse_fn();
        }

        return $ticker_response;
    }

    public function buildAggregateTickerResponse()
    {
        // this must be implemented in the calling class
        throw new Exception("Not implemented", 1);
    }

}

<?php

namespace Tokenly\CryptoQuoteClient\Drivers;

use Exception;
use Tokenly\CryptoQuoteClient\Drivers\Driver;
use Tokenly\CryptoQuoteClient\Quote;
use Tokenly\CryptoQuoteClient\Transport\Concerns\CachesAggregateTickerResponse;
use Tokenly\CryptoQuoteClient\Transport\Concerns\HasHttpTransportOptions;

/**
 * A crypto quote client
 */
class Poloniex implements Driver
{

    use HasHttpTransportOptions, CachesAggregateTickerResponse;


    public function getQuote($base, $target)
    {
        return $this->getQuotes([['base' => $base, 'target' => $target]])[0];
    }

    public function getQuotes($currency_pairs)
    {
        $aggregate_ticker_response = $this->aggregateTickerResponse();

        return $this->transformResult($aggregate_ticker_response, $currency_pairs);
    }

    public function getAllCurrencyPairs()
    {
        $aggregate_ticker_response = $this->aggregateTickerResponse();

        $currency_pairs = [];
        foreach ($aggregate_ticker_response as $market_ticker => $market) {
            [$base, $target] = explode('_', $market_ticker);
            if (in_array($base, ['BTC', 'ETH'])) {
                $currency_pairs[] = ['base' => $base, 'target' => $target];
            }
        }

        return $currency_pairs;
    }


    protected function aggregateTickerResponse()
    {
        return $this->getAggregateTickerResponseWithCache(function () {
            $result = $this->getHttpTransport()->getJSON('https://poloniex.com/public?command=returnTicker');

            if (!$result) {
                throw new Exception("Failed to get poloniex markets", 1);
            }

            return $result;
        });
    }

    protected function transformResult($aggregate_ticker_response, $currency_pairs)
    {
        $quotes_output = [];

        $currency_pairs_map = [];
        foreach ($currency_pairs as $offset => $currency_pair) {
            $base = $currency_pair['base'];
            $target = $currency_pair['target'];
            if (!in_array($base, ['BTC', 'ETH'])) {
                throw new Exception("Only bases of BTC and ETH are supported", 1);
            }

            $ticker_key = $base . '_' . $target;
            if (isset($aggregate_ticker_response[$ticker_key])) {
                $market = $aggregate_ticker_response[$ticker_key];

                $ask = $market['lowestAsk'];
                $bid = $market['highestBid'];
                $last = $market['last'];
                $timestamp = time();

                $quotes_output[$offset] = new Quote('poloniex', $base, $target, $ask, $bid, $last, $timestamp);
            } else {
                throw new Exception("Unknown poloniex currency pair: {$base}_{$target}", 1);
                
            }

        }

        return $quotes_output;
    }


/*
    public function getQuote($base, $target)
    {
        $quotes = $this->getQuotes([['base' => $base, 'target' => $target]]);
        return $quotes[0];
    }

    public function getQuotes($currency_pairs)
    {
        $result = $this->getHttpTransport()->getJSON('https://poloniex.com/public?command=returnTicker');
        return $this->transformResult($result, $currency_pairs);
    }

    protected function transformResult($result, $currency_pairs)
    {
        $quotes = [];
        foreach ($currency_pairs as $currency_pair) {
            $base = $currency_pair['base'];
            $target = $currency_pair['target'];

            $key = "${base}_${target}";
            if (!isset($result[$key])) {throw new Exception("Unknown currency pair for $base, $target", 1);}

            // values should be a float (not satoshis)
            $ask = $result[$key]['lowestAsk'];
            $bid = $result[$key]['highestBid'];
            $last = $result[$key]['last'];

            $timestamp = time();
            $quotes[] = new Quote('poloniex', $base, $target, $ask, $bid, $last, $timestamp);
        }

        return $quotes;
    }
*/
}

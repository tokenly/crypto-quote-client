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
class Bittrex implements Driver
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
        $result = $aggregate_ticker_response['result'];
        foreach ($result as $market) {
            [$base, $target] = explode('-', $market['MarketName']);
            if (in_array($base, ['BTC', 'ETH'])) {
                $currency_pairs[] = ['base' => $base, 'target' => $target];
            }
        }

        return $currency_pairs;
    }

    protected function aggregateTickerResponse()
    {
        return $this->getAggregateTickerResponseWithCache(function () {
            $result = $this->getHttpTransport()->getJSON('https://bittrex.com/api/v1.1/public/getmarketsummaries');

            if (!$result['success']) {
                throw new Exception("Failed to get bittrex markets: " . $result['message'] ?? 'no message', 1);
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

            $currency_pairs_map[$base . '-' . $target] = $offset;
        }

        $result = $aggregate_ticker_response['result'];
        foreach ($result as $market) {
            if (isset($currency_pairs_map[$market['MarketName']])) {
                $offset = $currency_pairs_map[$market['MarketName']];

                $ask = $market['Ask'];
                $bid = $market['Bid'];
                $last = $market['Last'];
                $timestamp = time();

                $quotes_output[$offset] = new Quote('bittrex', $base, $target, $ask, $bid, $last, $timestamp);
            }
        }

        // check for missing quotes output
        if (count($quotes_output) != count($currency_pairs)) {
            foreach ($currency_pairs as $offset => $currency_pair) {
                if (!isset($quotes_output[$offset])) {
                    $base = $currency_pair['base'];
                    $target = $currency_pair['target'];
                    throw new Exception("Unknown bittrex currency pair: {$base}-{$target}", 1);
                }
            }
        }

        return $quotes_output;
    }

}

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
class Binance implements Driver
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

    protected function aggregateTickerResponse()
    {
        return $this->getAggregateTickerResponseWithCache(function () {
            $price_ticker = $this->getHttpTransport()->getJSON('https://api.binance.com/api/v3/ticker/price');
            $book_ticker = $this->getHttpTransport()->getJSON('https://api.binance.com/api/v3/ticker/bookTicker');

            if (!$price_ticker or !$book_ticker) {
                throw new Exception("Failed to get binance markets", 1);
            }

            return [$price_ticker, $book_ticker];
        });
    }

    public function getAllCurrencyPairs()
    {
        [$price_ticker, $book_ticker] = $this->aggregateTickerResponse();

        $currency_pairs = [];
        foreach ($price_ticker as $market) {
            $symbol = $market['symbol'];
            $base = substr($symbol, -3);
            if (in_array($base, ['BTC', 'ETH'])) {
                $target = substr($symbol, 0, -3);
                $currency_pairs[] = ['base' => $base, 'target' => $target];
            }
        }

        return $currency_pairs;
    }

    protected function transformResult($aggregate_ticker_response, $currency_pairs)
    {
        [$price_ticker, $book_ticker] = $aggregate_ticker_response;

        $quotes_output = [];

        $currency_pairs_map = [];
        foreach ($currency_pairs as $offset => $currency_pair) {
            $base = $currency_pair['base'];
            $target = $currency_pair['target'];

            if (!in_array($base, ['BTC', 'ETH'])) {
                throw new Exception("Only bases of BTC and ETH are supported", 1);
            }

            $currency_pairs_map[$target . $base] = $offset;
        }

        foreach ($book_ticker as $market) {
            if (isset($currency_pairs_map[$market['symbol']])) {
                $offset = $currency_pairs_map[$market['symbol']];

                $timestamp = time();
                $ask = $market['askPrice'];
                $bid = $market['bidPrice'];
                
                $last = null;
                foreach ($price_ticker as $price) {
                    if ($price['symbol'] == $market['symbol']) {
                        $last = $price['price'];
                    }
                }

                $quotes_output[$offset] = new Quote('binance', $base, $target, $ask, $bid, $last, $timestamp);
            }
        }

        // check for missing quotes output
        if (count($quotes_output) != count($currency_pairs)) {
            foreach ($currency_pairs as $offset => $currency_pair) {
                if (!isset($quotes_output[$offset])) {
                    $base = $currency_pair['base'];
                    $target = $currency_pair['target'];
                    throw new Exception("Unknown binance currency pair: {$base}-{$target}", 1);
                }
            }
        }

        return $quotes_output;
    }

}

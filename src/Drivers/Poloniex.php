<?php

namespace Tokenly\CryptoQuoteClient\Drivers;

use Exception;
use Tokenly\CryptoQuoteClient\Drivers\Driver;
use Tokenly\CryptoQuoteClient\Quote;
use Tokenly\CryptoQuoteClient\Transport\Http;

/**
 * A crypto quote client
 */
class Poloniex implements Driver
{

    public function getQuote($base, $target)
    {
        $quotes = $this->getQuotes([['base' => $base, 'target' => $target]]);
        return $quotes[0];
    }

    public function getQuotes($currency_pairs)
    {
        $transport = new Http();
        $result = $transport->getJSON('https://poloniex.com/public?command=returnTicker');
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

}

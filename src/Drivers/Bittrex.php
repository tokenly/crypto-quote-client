<?php

namespace Tokenly\CryptoQuoteClient\Drivers;

use Exception;
use Tokenly\CryptoQuoteClient\Drivers\Driver;
use Tokenly\CryptoQuoteClient\Quote;
use Tokenly\CryptoQuoteClient\Transport\Http;

/**
 * A crypto quote client
 */
class Bittrex implements Driver
{

    public function getQuote($base, $target)
    {
        if (!in_array($base, ['BTC'])) {throw new Exception("Only a base of BTC is supported", 1);}

        $transport = new Http();
        $result = $transport->getJSON('https://bittrex.com/api/v1.1/public/getticker?market='.$base.'-'.$target.'');
        if (!$result OR !is_array($result)) {
            throw new Exception("Unable to find data for $target", 1);
        }

        return $this->transformResult($base, $target, $result);
    }


    public function getQuotes($currency_pairs)
    {
        if (count($currency_pairs) > 1) {throw new Exception("Only 1 currency pair is allowed", 1);}
        $currency_pair = $currency_pairs[0];

        $base = $currency_pair['base'];
        $target = $currency_pair['target'];
        return [$this->getQuote($base, $target)];
    }

    protected function transformResult($base, $target, $raw_result)
    {
        $result = $raw_result['result'];

        $ask = $result['Ask'];
        $bid = $result['Bid'];
        $last = $result['Last'];
        $timestamp = time();
        return new Quote('bittrex', $base, $target, $ask, $bid, $last, $timestamp);
    }

}

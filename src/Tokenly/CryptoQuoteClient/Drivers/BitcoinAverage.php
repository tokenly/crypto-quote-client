<?php 

namespace Tokenly\CryptoQuoteClient\Drivers;

use Tokenly\CryptoQuoteClient\Drivers\Driver;
use Tokenly\CryptoQuoteClient\Quote;
use Tokenly\CryptoQuoteClient\Transport\Http;
use Exception;

/**
* A crypto quote client
*/
class BitcoinAverage implements Driver
{

    public function getQuote($base, $target) {
        if (!in_array($base,['USD','EUR'])) { throw new Exception("Only a base of USD or EUR is supported", 1); }
        if ($target !== 'BTC') { throw new Exception("Only a target of BTC is supported", 1); }

        $transport = new Http();
        $result = $transport->getJSON('https://api.bitcoinaverage.com/ticker/global/'.$base.'/');

        return $this->transformResult($base, $result);
    }

    public function getQuotes($currency_pairs) {
        if (count($currency_pairs) > 1) { throw new Exception("Only 1 currency pair is allowed", 1); }
        $currency_pair = $currency_pairs[0];

        $base   = $currency_pair['base'];
        $target = $currency_pair['target'];
        return [$this->getQuote($base, $target)];
    }


    protected function transformResult($base, $result) {
        $target = 'BTC';

        // values should be a float (not satoshis)
        $ask  = $result['ask'];
        $bid  = $result['bid'];
        $last  = $result['last'];

        $timestamp = time();
        return new Quote('bitcoinAverage', $base, $target, $ask, $bid, $last, $timestamp);
    }

}
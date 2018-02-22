<?php

namespace Tokenly\CryptoQuoteClient\Drivers;

use Exception;
use Tokenly\CryptoQuoteClient\Drivers\Driver;
use Tokenly\CryptoQuoteClient\Quote;
use Tokenly\CryptoQuoteClient\Transport\Http;

/**
 * A crypto quote client driver
 */
class CoinMarketCap implements Driver
{

    public function getQuote($base, $target)
    {
        if (!in_array($base, ['USD', 'BTC'])) {throw new Exception("Only a base of USD or BTC is supported", 1);}

        $transport = new Http();
        $result = $transport->getJSON('https://api.coinmarketcap.com/v1/ticker/' . $target . '/');
        if (!$result OR !is_array($result)) {
            throw new Exception("Unable to find data for $target", 1);
        }

        return $this->transformResult($base, $target, $result[0]);
    }

    public function getQuotes($currency_pairs)
    {
        if (count($currency_pairs) > 1) {throw new Exception("Only 1 currency pair is allowed", 1);}
        $currency_pair = $currency_pairs[0];

        $base = $currency_pair['base'];
        $target = $currency_pair['target'];
        return [$this->getQuote($base, $target)];
    }

    protected function transformResult($base, $target, $result)
    {
        switch ($base) {
            case 'BTC':
                $price = $result['price_btc'];
                break;
            case 'USD':
                $price = $result['price_usd'];
                break;
            
            default:
                throw new Exception("Unknown base: $base", 1);
        }

        // values should be a float (not satoshis)
        $ask = $price;
        $bid = $price;
        $last = $price;
        $timestamp = time();

        return new Quote('coinMarketCap', $base, $target, $ask, $bid, $last, $timestamp);
    }

}

<?php 

namespace Tokenly\CryptoQuoteClient;

use ArrayObject;
use Tokenly\CurrencyLib\CurrencyUtil;

/**
* A Data object representing the quote
*/
class Quote extends ArrayObject
{
    
    function __construct($name, $base, $target, $ask, $bid, $last, $timestamp=null)
    {
        return parent::__construct($this->buildValues($name, $base, $target, $ask, $bid, $last, $timestamp));
    }

    protected function buildValues($name, $base, $target, $ask, $bid, $last, $timestamp=null) {
        $out = [];

        $out['name']      = $name;

        $out['base']      = $base;
        $out['target']    = $target;

        $out['ask']       = floatval($ask);
        $out['askSat']    = CurrencyUtil::valueToSatoshis($ask);

        $out['bid']       = floatval($bid);
        $out['bidSat']    = CurrencyUtil::valueToSatoshis($bid);

        $out['last']      = floatval($last);
        $out['lastSat']   = CurrencyUtil::valueToSatoshis($last);

        $out['timestamp'] = ($timestamp === null ? time() : intval($timestamp));

        return $out;
    }
}
<?php

namespace Tokenly\CryptoQuoteClient;

use ArrayObject;
use Tokenly\CryptoQuantity\CryptoQuantity;

/**
 * A Data object representing the quote
 */
class Quote extends ArrayObject
{

    public function __construct($name, $base, $target, $ask, $bid, $last, $timestamp = null)
    {
        return parent::__construct($this->buildValues($name, $base, $target, $ask, $bid, $last, $timestamp));
    }

    protected function buildValues($name, $base, $target, $ask, $bid, $last, $timestamp = null)
    {
        $out = [];

        $out['name'] = $name;

        $out['base'] = $base;
        $out['target'] = $target;

        $out['ask'] = floatval($ask);
        $out['askSat'] = CryptoQuantity::valueToSatoshis($ask);

        $out['bid'] = floatval($bid);
        $out['bidSat'] = CryptoQuantity::valueToSatoshis($bid);

        $out['last'] = floatval($last);
        $out['lastSat'] = CryptoQuantity::valueToSatoshis($last);

        $out['timestamp'] = ($timestamp === null ? time() : intval($timestamp));

        return $out;
    }
}

<?php

namespace Tokenly\CryptoQuoteClient;

use Exception;

/**
 * A crypto quote client
 */
class Client
{

    public function __construct()
    {

    }

    public function getQuote($driver_name, $base, $target, $options = null)
    {
        $driver = $this->getDriver($driver_name);
        if ($options !== null) {
            $driver->setTransportOptions($options);
        }
        return $driver->getQuote($base, $target);
    }

    public function getQuotes($driver_name, $currency_pairs, $options = null)
    {
        $driver = $this->getDriver($driver_name);
        if ($options !== null) {
            $driver->setTransportOptions($options);
        }
        return $driver->getQuotes($currency_pairs);
    }

    public function getDriver($driver_name)
    {
        $driver_class_name = ucwords($driver_name);
        $class = "Tokenly\\CryptoQuoteClient\\Drivers\\{$driver_class_name}";
        if (!class_exists($class)) {throw new Exception("Unable to build driver for $driver_name ($class)", 1);}

        return new $class();
    }

}

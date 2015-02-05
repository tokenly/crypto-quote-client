<?php 

namespace Tokenly\CryptoQuoteClient\Drivers;

/**
* A crypto quote client
*/
interface Driver
{

    public function getQuote($base, $target);
    
    public function getQuotes($currency_pairs);


}
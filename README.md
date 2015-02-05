Gets quotes for cryptocurrency prices.

[![Build Status](https://travis-ci.org/tokenly/crypto-quote-client.svg)](https://travis-ci.org/tokenly/crypto-quote-client)

Usage:
```php
$client = new Tokenly\CryptoQuoteClient\Client();
$quote = $client->getQuote('bitcoinAverage', 'USD', 'BTC');
echo json_encode($quote, 192)."\n";

/*

{
    "name": "bitcoinAverage",
    "base": "USD",
    "target": "BTC",
    "ask": 217.55,
    "askSat": 21755000000,
    "bid": 217.19,
    "bidSat": 21719000000,
    "last": 217.39,
    "lastSat": 21739000000,
    "timestamp": 1423163845
}

*/
```

Included drivers are:

- bitcoinAverage
- bitstamp
- poloniex


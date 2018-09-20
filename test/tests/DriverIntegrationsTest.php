<?php

use Tokenly\CryptoQuoteClient\Client;
use \PHPUnit_Framework_Assert as PHPUnit;

/*
* 
*/
class DriverIntegrationsTest extends \PHPUnit_Framework_TestCase
{


    public function testBittrexDriver() {
        $client = $this->getQuoteClient();
        $quotes = $client->getQuotes('bittrex', [['base' => 'BTC', 'target' => 'BCH'], ['base' => 'BTC', 'target' => 'ETH']]);
        if (getenv('ECHO_QUOTES')) { echo "\$quotes:\n".json_encode($quotes, 192)."\n"; }
        PHPUnit::assertInstanceOf('Tokenly\CryptoQuoteClient\Quote', $quotes[0]);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['bid']);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['ask']);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['last']);

        $quote = $client->getQuote('bittrex', 'BTC', 'BCH');
        if (getenv('ECHO_QUOTES')) { echo "\$quote:\n".json_encode($quote, 192)."\n"; }
        PHPUnit::assertGreaterThan(0.00000025, $quote['bid']);
        PHPUnit::assertGreaterThan(0.00000025, $quote['ask']);
        PHPUnit::assertGreaterThan(0.00000025, $quote['last']);
    }

    public function testPoloniexDriver() {
        $client = $this->getQuoteClient();
        $quotes = $client->getQuotes('poloniex', [['base' => 'BTC', 'target' => 'BCH'], ['base' => 'ETH', 'target' => 'EOS']]);
        if (getenv('ECHO_QUOTES')) { echo "\$quotes:\n".json_encode($quotes, 192)."\n"; }
        PHPUnit::assertInstanceOf('Tokenly\CryptoQuoteClient\Quote', $quotes[0]);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['bid']);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['ask']);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['last']);

        $quote = $client->getQuote('poloniex', 'BTC', 'BCH');
        if (getenv('ECHO_QUOTES')) { echo "\$quote:\n".json_encode($quote, 192)."\n"; }
        PHPUnit::assertGreaterThan(0.00000025, $quote['bid']);
        PHPUnit::assertGreaterThan(0.00000025, $quote['ask']);
        PHPUnit::assertGreaterThan(0.00000025, $quote['last']);
    }

    public function testBinanceDriver() {
        $client = $this->getQuoteClient();
        $quotes = $client->getQuotes('binance', [['base' => 'BTC', 'target' => 'LTC'], ['base' => 'ETH', 'target' => 'EOS']]);
        if (getenv('ECHO_QUOTES')) { echo "\$quotes:\n".json_encode($quotes, 192)."\n"; }
        PHPUnit::assertInstanceOf('Tokenly\CryptoQuoteClient\Quote', $quotes[0]);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['bid']);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['ask']);
        PHPUnit::assertGreaterThan(0.00000025, $quotes[0]['last']);

        $quote = $client->getQuote('binance', 'BTC', 'LTC');
        if (getenv('ECHO_QUOTES')) { echo "\$quote:\n".json_encode($quote, 192)."\n"; }
        PHPUnit::assertGreaterThan(0.00000025, $quote['bid']);
        PHPUnit::assertGreaterThan(0.00000025, $quote['ask']);
        PHPUnit::assertGreaterThan(0.00000025, $quote['last']);
    }

    public function testBitcoinAverageDriver() {
        $client = $this->getQuoteClient();
        $quotes = $client->getQuotes('bitcoinAverage', [['base' => 'USD', 'target' => 'BTC']]);
        if (getenv('ECHO_QUOTES')) { echo "\$quotes:\n".json_encode($quotes, 192)."\n"; }
        PHPUnit::assertInstanceOf('Tokenly\CryptoQuoteClient\Quote', $quotes[0]);
        PHPUnit::assertGreaterThan(100, $quotes[0]['bid']);
        PHPUnit::assertGreaterThan(100, $quotes[0]['ask']);
        PHPUnit::assertGreaterThan(100, $quotes[0]['last']);

        $quote = $client->getQuote('bitcoinAverage', 'USD', 'BTC');
        if (getenv('ECHO_QUOTES')) { echo "\$quote:\n".json_encode($quote, 192)."\n"; }
        PHPUnit::assertGreaterThan(100, $quote['bid']);
        PHPUnit::assertGreaterThan(100, $quote['ask']);
        PHPUnit::assertGreaterThan(100, $quote['last']);

        $quote = $client->getQuote('bitcoinAverage', 'EUR', 'BTC');
        if (getenv('ECHO_QUOTES')) { echo "\$quote:\n".json_encode($quote, 192)."\n"; }
        PHPUnit::assertGreaterThan(100, $quote['bid']);
        PHPUnit::assertGreaterThan(100, $quote['ask']);
        PHPUnit::assertGreaterThan(100, $quote['last']);
    }


    public function testBitstampDriver() {
        $client = $this->getQuoteClient();
        $quotes = $client->getQuotes('bitstamp', [['base' => 'USD', 'target' => 'BTC']]);
        if (getenv('ECHO_QUOTES')) { echo "\$quotes:\n".json_encode($quotes, 192)."\n"; }
        PHPUnit::assertInstanceOf('Tokenly\CryptoQuoteClient\Quote', $quotes[0]);
        PHPUnit::assertGreaterThan(100, $quotes[0]['bid']);
        PHPUnit::assertGreaterThan(100, $quotes[0]['ask']);
        PHPUnit::assertGreaterThan(100, $quotes[0]['last']);

        $quote = $client->getQuote('bitstamp', 'USD', 'BTC');
        if (getenv('ECHO_QUOTES')) { echo "\$quote:\n".json_encode($quote, 192)."\n"; }
        PHPUnit::assertGreaterThan(100, $quote['bid']);
        PHPUnit::assertGreaterThan(100, $quote['ask']);
        PHPUnit::assertGreaterThan(100, $quote['last']);

        $quote = $client->getQuote('bitstamp', 'EUR', 'BTC');
        if (getenv('ECHO_QUOTES')) { echo "\$quote:\n".json_encode($quote, 192)."\n"; }
        PHPUnit::assertGreaterThan(100, $quote['bid']);
        PHPUnit::assertGreaterThan(100, $quote['ask']);
        PHPUnit::assertGreaterThan(100, $quote['last']);
    }

    protected function getQuoteClient() {
        if (!isset($this->quote_client)) {
            $this->quote_client = new Client();
        }
        return $this->quote_client;
    }

}

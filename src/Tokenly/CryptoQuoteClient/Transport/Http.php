<?php

namespace Tokenly\CryptoQuoteClient\Transport;

use Exception;
use GuzzleHttp\Client as GuzzleClient;

/**
* An http transport for getting crypto quotes
*/
class Http
{

    // 5 second timeout by default
    var $timeout = 5;

    public function getJSON($url) {
        $json = $this->request('GET', $url)->json();
        if (!is_array($json)) { throw new Exception("Unexpected response", 1); }

        return $json;
    }

    public function request($method, $url) {
        $client = new GuzzleClient();

        // set options
        $options = [
            'timeout' => $this->timeout,
        ];

        $request = $client->createRequest($method, $url, $options);

        // return the response
        return $client->send($request);

    }

}
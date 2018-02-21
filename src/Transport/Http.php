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
        $raw_body = $this->request('GET', $url)->getBody();

        $json = json_decode($raw_body, true);
        if (!is_array($json)) { throw new Exception("Unexpected response body", 1); }

        return $json;
    }

    public function request($method, $url) {
        $client = new GuzzleClient();

        // set options
        $options = [
            'timeout' => $this->timeout,
        ];

        $response = $client->request($method, $url, $options);

        // return the response
        return $response;

    }

}
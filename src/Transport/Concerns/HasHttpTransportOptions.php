<?php

namespace Tokenly\CryptoQuoteClient\Transport\Concerns;

use Tokenly\CryptoQuoteClient\Transport\Http;

/**
 * An http transport for getting crypto quotes
 */
trait HasHttpTransportOptions
{

    protected $transport_options = null;

    public function setTransportOptions($options)
    {
        $this->transport_options = $options;
        return $this;
    }

    public function getHttpTransport()
    {
        $transport = new Http();
        $transport->setOptions($this->transport_options);
        return $transport;
    }

}

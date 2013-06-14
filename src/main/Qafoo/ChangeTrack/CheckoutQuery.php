<?php

namespace Qafoo\ChangeTrack;

class CheckoutQuery
{
    private $query;
    private $checkout;
    private $currentVersion;

    public function __construct($query, $checkout)
    {
        $this->query = $query;
        $this->checkout = $checkout;
    }

    public function find($version, $path)
    {
        if ($this->currentVersion !== $version) {
            $this->checkout->update($version);
            $this->currentVersion = $version;
        }

        return $this->query->find($path);
    }
}

<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Arbit\VCSWrapper;

class CheckoutFactory
{
    /**
     * @param string $repositoryUrl
     * @param string $checkoutPath
     * @param string $cachePath
     */
    public function createCheckout($repositoryUrl, $checkoutPath, $cachePath)
    {
        VCSWrapper\Cache\Manager::initialize($cachePath);

        $checkout = new VCSWrapper\GitCli\Checkout($checkoutPath);
        $checkout->initialize($repositoryUrl);

        return $checkout;
    }
}

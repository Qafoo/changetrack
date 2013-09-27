<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Arbit\VCSWrapper;
use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

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

        $checkout = new GitCheckout($checkoutPath);
        $checkout->initialize($repositoryUrl);

        return $checkout;
    }
}

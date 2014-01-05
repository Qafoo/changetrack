<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Arbit\VCSWrapper;
use Qafoo\ChangeTrack\Analyzer\Checkout\GitCheckout;
use Qafoo\ChangeTrack\Analyzer\Checkout\VcsWrapperDiffMapper;

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

        $diffMapper = new VcsWrapperDiffMapper();

        $checkout = new GitCheckout($checkoutPath, $diffMapper);
        $checkout->initialize($repositoryUrl);

        return $checkout;
    }
}

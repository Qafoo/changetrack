<?php

namespace Qafoo\ChangeTrack;

use pdepend\reflection\ReflectionSession;

use Qafoo\ChangeTrack\Analyzer\CheckoutFactory;
use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;
use Qafoo\ChangeTrack\Analyzer\ChangeFeed;
use Qafoo\ChangeTrack\Analyzer\ResultBuilder;
use Qafoo\ChangeTrack\Analyzer\DiffIterator;
use Qafoo\ChangeTrack\Analyzer\LineFeed\ChunksLineFeedIterator;

class Analyzer
{
    private $checkout;

    private $checkoutPath;

    private $repositoryUrl;

    public function __construct($repositoryUrl, $checkoutPath, $cachePath)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->checkoutPath = $checkoutPath;

        $checkoutFactory = new CheckoutFactory();
        $this->checkout = $checkoutFactory->createCheckout($repositoryUrl, $checkoutPath, $cachePath);
    }

    public function analyze()
    {
        $session = new ReflectionSession();
        $query = $session->createFileQuery();

        $changeFeed = new ChangeFeed($this->checkout);
        $resultBuilder = new ResultBuilder($this->repositoryUrl);
        $changeRecorder = new ChangeRecorder($query, $resultBuilder);

        foreach ($changeFeed as $changeSet) {
            $changeSet->recordChanges($changeRecorder);
        }
        return $resultBuilder->buildResult();
    }
}

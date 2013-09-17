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
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\CheckoutFactory
     */
    private $checkoutFactory;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\CheckoutFactory $checkoutFactory
     */
    public function __construct(CheckoutFactory $checkoutFactory)
    {
        $this->checkoutFactory = $checkoutFactory;
    }

    public function analyze($repositoryUrl, $checkoutPath, $cachePath)
    {
        $checkout = $this->checkoutFactory->createCheckout($repositoryUrl, $checkoutPath, $cachePath);

        $session = new ReflectionSession();
        $query = $session->createFileQuery();

        $changeFeed = new ChangeFeed($checkout);
        $resultBuilder = new ResultBuilder($repositoryUrl);
        $changeRecorder = new ChangeRecorder($query, $resultBuilder);

        foreach ($changeFeed as $changeSet) {
            $changeSet->recordChanges($changeRecorder);
        }
        return $resultBuilder->buildResult();
    }
}

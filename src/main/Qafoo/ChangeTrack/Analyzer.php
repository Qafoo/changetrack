<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\Analyzer\CheckoutFactory;
use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;
use Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedFactory;
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
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeFeedFactory
     */
    private $changeFeedFactory;

    /**
     * @var string
     */
    private $workingPath;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\CheckoutFactory $checkoutFactory
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeFeedFactory $changeFeedFactory
     * @param string $workingPath
     */
    public function __construct(
        CheckoutFactory $checkoutFactory,
        ChangeFeedFactory $changeFeedFactory,
        $workingPath
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->changeFeedFactory = $changeFeedFactory;
        $this->workingPath = $workingPath;
    }

    public function analyze($repositoryUrl, $startRevision = null, $endRevision = null)
    {
        $checkout = $this->createCheckout($repositoryUrl);

        $changeFeed = $this->changeFeedFactory->createChangeFeed(
            $checkout,
            $startRevision,
            $endRevision
        );
        $resultBuilder = new ResultBuilder($repositoryUrl);
        $changeRecorder = new ChangeRecorder($resultBuilder);

        foreach ($changeFeed as $changeSet) {
            $changeSet->recordChanges($changeRecorder);
        }
        return $resultBuilder->buildResult();
    }

    /**
     * @param string $repositoryUrl
     */
    private function createCheckout($repositoryUrl)
    {
        $checkoutPath = $this->workingPath . '/checkout';
        $cachePath = $this->workingPath . '/cache';

        mkdir($checkoutPath);
        mkdir($cachePath);

        return $this->checkoutFactory->createCheckout(
            $repositoryUrl,
            $checkoutPath,
            $cachePath
        );
    }
}

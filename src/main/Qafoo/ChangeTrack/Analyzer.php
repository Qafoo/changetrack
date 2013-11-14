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
    private $workingDir;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\CheckoutFactory $checkoutFactory
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeFeedFactory $changeFeedFactory
     * @param \Qafoo\ChangeTrack\TemporaryDirectory $workingDir
     */
    public function __construct(
        CheckoutFactory $checkoutFactory,
        ChangeFeedFactory $changeFeedFactory,
        TemporaryDirectory $workingDir
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->changeFeedFactory = $changeFeedFactory;
        $this->workingDir = $workingDir;
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

        $this->workingDir->cleanup();

        return $resultBuilder->buildResult();
    }

    /**
     * @param string $repositoryUrl
     */
    private function createCheckout($repositoryUrl)
    {
        $checkoutPath = $this->workingDir->createDirectory('checkout');
        $cachePath = $this->workingDir->createDirectory('cache');

        return $this->checkoutFactory->createCheckout(
            $repositoryUrl,
            $checkoutPath,
            $cachePath
        );
    }
}

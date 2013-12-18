<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\Analyzer\ChangeSet;
use Qafoo\ChangeTrack\Analyzer\PathFilter;
use Qafoo\ChangeTrack\Analyzer\RevisionBoundaries;
use Qafoo\ChangeTrack\Analyzer\CheckoutFactory;
use Qafoo\ChangeTrack\Analyzer\ChangeRecorderFactory;
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
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeRecorderFactory
     */
    private $changeRecorderFactory;

    /**
     * @var string
     */
    private $workingDir;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\CheckoutFactory $checkoutFactory
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeFeedFactory $changeFeedFactory
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeRecorderFactory $changeRecorderFactory
     * @param \Qafoo\ChangeTrack\TemporaryDirectory $workingDir
     */
    public function __construct(
        CheckoutFactory $checkoutFactory,
        ChangeFeedFactory $changeFeedFactory,
        ChangeRecorderFactory $changeRecorderFactory,
        TemporaryDirectory $workingDir
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->changeFeedFactory = $changeFeedFactory;
        $this->changeRecorderFactory = $changeRecorderFactory;
        $this->workingDir = $workingDir;
    }

    public function analyze(
        $repositoryUrl,
        RevisionBoundaries $boundaries,
        PathFilter $pathFilter
    ) {
        $checkout = $this->createCheckout($repositoryUrl);

        $changeFeed = $this->changeFeedFactory->createChangeFeed(
            $checkout,
            $boundaries
        );
        $resultBuilder = new ResultBuilder($repositoryUrl);

        $changeRecorder = $this->changeRecorderFactory->createChangeRecorder($resultBuilder);

        foreach ($changeFeed as $changeSet) {
            /** @var ChangeSet $changeSet */
            $changeSet->recordChanges(
                $changeRecorder,
                $pathFilter
            );
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

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
     * @var string
     */
    private $checkoutPath;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\CheckoutFactory $checkoutFactory
     * @param string $checkoutPath
     * @param string $cachePath
     */
    public function __construct(CheckoutFactory $checkoutFactory, $checkoutPath, $cachePath)
    {
        $this->checkoutFactory = $checkoutFactory;
        $this->checkoutPath = $checkoutPath;
        $this->cachePath = $cachePath;
    }

    public function analyze($repositoryUrl, $startRevision = null, $endRevision = null)
    {
        $checkout = $this->checkoutFactory->createCheckout(
            $repositoryUrl,
            $this->checkoutPath,
            $this->cachePath
        );

        $session = new ReflectionSession();
        $query = $session->createFileQuery();

        $changeFeed = new ChangeFeed($checkout, $startRevision, $endRevision);
        $resultBuilder = new ResultBuilder($repositoryUrl);
        $changeRecorder = new ChangeRecorder($query, $resultBuilder);

        foreach ($changeFeed as $changeSet) {
            $changeSet->recordChanges($changeRecorder);
        }
        return $resultBuilder->buildResult();
    }
}

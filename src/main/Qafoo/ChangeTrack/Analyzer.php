<?php

namespace Qafoo\ChangeTrack;

use pdepend\reflection\ReflectionSession;
use Qafoo\ChangeTrack\Analyzer\Reflection\NullSourceResolver;

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
        $beforeCheckout = $this->createCheckout($repositoryUrl, 'before');
        $afterCheckout = $this->createCheckout($repositoryUrl, 'after');

        $sourceResolver = new NullSourceResolver();
        $session = ReflectionSession::createDefaultSession($sourceResolver);
        $query = $session->createFileQuery();

        $changeFeed = $this->changeFeedFactory->createChangeFeed(
            $beforeCheckout,
            $afterCheckout,
            $startRevision,
            $endRevision
        );
        $resultBuilder = new ResultBuilder($repositoryUrl);
        $changeRecorder = new ChangeRecorder($query, $resultBuilder);

        foreach ($changeFeed as $changeSet) {
            $changeSet->recordChanges($changeRecorder);
        }
        return $resultBuilder->buildResult();
    }

    /**
     * Creates a checkout with the given $identifier
     *
     * @param string $repositoryUrl
     * @param string $identifier
     */
    private function createCheckout($repositoryUrl, $identifier)
    {
        $checkoutPath = $this->workingPath . '/' . $identifier . '_checkout';
        $cachePath = $this->workingPath . '/' . $identifier . '_cache';

        mkdir($checkoutPath);
        mkdir($cachePath);

        return $this->checkoutFactory->createCheckout(
            $repositoryUrl,
            $checkoutPath,
            $cachePath
        );
    }
}

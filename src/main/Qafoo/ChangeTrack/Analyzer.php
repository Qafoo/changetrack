<?php

namespace Qafoo\ChangeTrack;

use pdepend\reflection\ReflectionSession;
use Arbit\VCSWrapper;

use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;
use Qafoo\ChangeTrack\Analyzer\DiffIterator;
use Qafoo\ChangeTrack\Analyzer\LineFeed\ChunksLineFeedIterator;

class Analyzer
{
    private $checkout;

    private $checkoutPath;

    public function __construct($repositoryUrl, $checkoutPath, $cachePath)
    {
        VCSWrapper\Cache\Manager::initialize($cachePath);

        $this->checkoutPath = $checkoutPath;

        $this->checkout = new VCSWrapper\GitCli\Checkout($this->checkoutPath);
        $this->checkout->initialize($repositoryUrl);
    }

    public function analyze()
    {
        $session = new ReflectionSession();
        $query = $session->createFileQuery();

        $changeFeed = new ChangeFeed($this->checkout);
        $changeRecorder = new ChangeRecorder($query);

        foreach ($changeFeed as $changeSet) {
            $changeSet->recordChanges($changeRecorder);
        }
        return $changeRecorder->getChanges();
    }
}

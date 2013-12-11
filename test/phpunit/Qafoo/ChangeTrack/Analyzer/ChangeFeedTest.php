<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Arbit\VCSWrapper;
use Qafoo\ChangeTrack\Analyzer\RevisionBoundaries;
use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;
use Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver\NullObserver;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\ChangeSetFactory;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffIteratorFactory;

use Qafoo\ChangeTrack\CheckoutAwareTestBase;

/**
 * @group integration
 */
class ChangeFeedTest extends CheckoutAwareTestBase
{
    /**
     * @var \Arbit\VCSWrapper\Checkout
     */
    private $checkout;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver\NullObserver
     */
    private $observerDummy;

    private $changeSetFactory;

    public function setup()
    {
        parent::setup();

        VCSWrapper\Cache\Manager::initialize($this->getCachePath());
        $this->checkout = new GitCheckout($this->getCheckoutPath());
        $this->checkout->initialize($this->getRepositoryUrl());

        $this->changeSetFactory = new ChangeSetFactory(
            new DiffIteratorFactory()
        );

        $this->observerDummy = new NullObserver();
    }

    private function createChangeFeed($startRevision = null, $endRevision = null)
    {
        return new ChangeFeed(
            $this->checkout,
            $this->changeSetFactory,
            $this->observerDummy,
            new RevisionBoundaries($startRevision, $endRevision)
        );
    }

    /**
     * @test
     */
    public function can_iterate_full_log()
    {
        $changeFeed = $this->createChangeFeed();

        $counter = 0;
        foreach ($changeFeed as $changeSet) {
            $counter++;
        }

        $this->assertEquals(11, $counter);
    }

    /**
     * @test
     */
    public function always_returns_changeset()
    {
        $changeFeed = $this->createChangeFeed();

        foreach ($changeFeed as $changeSet) {
            $this->assertInstanceOf(
                'Qafoo\\ChangeTrack\\Analyzer\\ChangeSet',
                $changeSet
            );
        }
    }

    /**
     * @test
     */
    public function first_change_set_is_no_more_initial()
    {
        $changeFeed = $this->createChangeFeed();

        $this->assertInstanceOf(
            'Qafoo\\ChangeTrack\\Analyzer\\ChangeSet\\DiffChangeSet',
            $changeFeed->current()
        );
    }

    /**
     * @test
     */
    public function starts_iteration_from_later_revision_if_provided()
    {
        $changeFeed = $this->createChangeFeed(
            'bb6f4f102ebaad2b8151bb44929eadce298e8ec9'
        );

        $this->assertEquals(
            'bb6f4f102ebaad2b8151bb44929eadce298e8ec9',
            $changeFeed->key()
        );
    }

    /**
     * @test
     */
    public function ends_iteration_at_earlier_revision_if_provided()
    {
        $changeFeed = $this->createChangeFeed(
            null,
            'bb6f4f102ebaad2b8151bb44929eadce298e8ec9'
        );

        $this->assertEquals(
            '524fa0032429821a63042217f5579197c9e319bc',
            $changeFeed->key()
        );
        foreach ($changeFeed as $revision => $changeSet) {
            // iterate through
        }

        $this->assertEquals(
            'bb6f4f102ebaad2b8151bb44929eadce298e8ec9',
            $revision
        );
    }
}

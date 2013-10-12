<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Arbit\VCSWrapper;
use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;
use Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver\NullObserver;

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

    public function setup()
    {
        parent::setup();

        VCSWrapper\Cache\Manager::initialize($this->getCachePath());
        $this->checkout = new GitCheckout($this->getCheckoutPath());
        $this->checkout->initialize($this->getRepositoryUrl());

        $this->observerDummy = new NullObserver();
    }

    /**
     * @test
     */
    public function can_iterate_full_log()
    {
        $changeFeed = new ChangeFeed($this->checkout, $this->observerDummy);

        $counter = 0;
        foreach ($changeFeed as $changeSet) {
            $counter++;
        }

        $this->assertEquals(10, $counter);
    }

    /**
     * @test
     */
    public function always_returns_changeset()
    {
        $changeFeed = new ChangeFeed($this->checkout, $this->observerDummy);

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
        $changeFeed = new ChangeFeed($this->checkout, $this->observerDummy);

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
        $changeFeed = new ChangeFeed(
            $this->checkout,
            $this->observerDummy,
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
        $changeFeed = new ChangeFeed(
            $this->checkout,
            $this->observerDummy,
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

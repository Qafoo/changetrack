<?php

namespace Qafoo\ChangeTrack\Analyzer\Checkout;

use Arbit\VCSWrapper;
use Qafoo\ChangeTrack\CheckoutAwareTestBase;

class GitCheckoutTest extends CheckoutAwareTestBase
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Checkout\GitCheckout
     */
    private $checkout;

    public function setup()
    {
        parent::setup();

        VCSWrapper\Cache\Manager::initialize($this->getCachePath());
        $this->checkout = new GitCheckout($this->getCheckoutPath());
        $this->checkout->initialize($this->getRepositoryUrl());
    }

    /**
     * @test
     */
    public function has_a_predecessor_commit()
    {
        $this->assertTrue(
            $this->checkout->hasPredecessor('de6bbebd2b0a8f70af2182c47fe3cca106dcd072')
        );
    }

    /**
     * @test
     */
    public function has_no_predecessor_commit()
    {
        $this->assertFalse(
            $this->checkout->hasPredecessor('524fa0032429821a63042217f5579197c9e319bc')
        );
    }

    /**
     * @test
     */
    public function gets_predecessor_commit()
    {
        $this->assertEquals(
            '7982af03f25becdaedb0f6030dfb255f5b499be9',
            $this->checkout->getPredecessor('de6bbebd2b0a8f70af2182c47fe3cca106dcd072')
        );
    }
}

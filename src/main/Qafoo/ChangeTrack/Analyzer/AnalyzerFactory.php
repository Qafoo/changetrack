<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer;

class AnalyzerFactory
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

    /**
     * @param string $workingPath
     * @return \Qafoo\ChangeTrack\Analyzer
     */
    public function createAnalyzer($workingPath)
    {
        return new Analyzer($this->checkoutFactory, $workingPath);
    }
}

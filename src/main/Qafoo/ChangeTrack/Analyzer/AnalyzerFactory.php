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
     * @param string $checkoutPath
     * @param string $cachePath
     * @return \Qafoo\ChangeTrack\Analyzer
     */
    public function createAnalyzer($checkoutPath, $cachePath)
    {
        return new Analyzer($this->checkoutFactory, $checkoutPath, $cachePath);
    }
}

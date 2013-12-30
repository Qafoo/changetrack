<?php

namespace Qafoo\ChangeTrack\FISCalculator\Renderer;

use Qafoo\ChangeTrack\FISCalculator\Renderer;
use Qafoo\ChangeTrack\FISCalculator\FrequentItemSetCollection;

use JMS\Serializer\SerializerInterface;

class JmsSerializerRenderer extends Renderer
{
    /**
     * @var JMS\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * __construct
     *
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\FrequentItemSetCollection $frequentItemSets
     */
    public function renderOutput(FrequentItemSetCollection $frequentItemSets)
    {
        return $this->serializer->serialize($frequentItemSets, 'xml');
    }
}

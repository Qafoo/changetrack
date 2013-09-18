<?php

namespace Qafoo\ChangeTrack\Calculator\Renderer;

use Qafoo\ChangeTrack\Calculator\Renderer;
use Qafoo\ChangeTrack\Calculator\Stats;

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
     * @param \Qafoo\ChangeTrack\Calculator\Stats $statistics
     */
    public function renderOutput(Stats $statistics)
    {
        return $this->serializer->serialize($statistics, 'xml');
    }
}

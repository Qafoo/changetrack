<?php

namespace Qafoo\ChangeTrack\Calculator\Renderer;

use Qafoo\ChangeTrack\Calculator\Renderer;
use Qafoo\ChangeTrack\Calculator\Stats;

class JmsSerializerRenderer extends Renderer
{
    /**
     *
     * @param \Qafoo\ChangeTrack\Calculator\Stats $statistics
     */
    public function renderOutput(Stats $statistics)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/../../../../../config/jmsserializer', 'Qafoo\\ChangeTrack')
            ->build();
        return $serializer->serialize($statistics, 'xml');
    }
}

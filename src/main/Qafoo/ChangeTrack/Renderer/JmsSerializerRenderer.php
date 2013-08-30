<?php

namespace Qafoo\ChangeTrack\Renderer;

use Qafoo\ChangeTrack\Renderer;
use Qafoo\ChangeTrack\Change;
use Qafoo\ChangeTrack\Analyzer\Result;

class JmsSerializerRenderer extends Renderer
{
    /**
     * Render the output of $analysisResult into a string and return it.
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Result $analysisResult
     * @return string
     */
    public function renderOutput(Result $analysisResult)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/../../../../config/jmsserializer', 'Qafoo\\ChangeTrack')
            ->build();
        return $serializer->serialize($analysisResult, 'xml');
    }
}

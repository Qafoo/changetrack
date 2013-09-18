<?php

namespace Qafoo\ChangeTrack\Analyzer\Renderer;

use Qafoo\ChangeTrack\Analyzer\Renderer;
use Qafoo\ChangeTrack\Analyzer\Change;
use Qafoo\ChangeTrack\Analyzer\Result;

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
     * Render the output of $analysisResult into a string and return it.
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Result $analysisResult
     * @return string
     */
    public function renderOutput(Result $analysisResult)
    {
        return $this->serializer->serialize($analysisResult, 'xml');
    }
}

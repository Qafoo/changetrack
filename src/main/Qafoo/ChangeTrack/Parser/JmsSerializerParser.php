<?php

namespace Qafoo\ChangeTrack\Parser;

use Qafoo\ChangeTrack\Parser;

use JMS\Serializer\SerializerInterface;

class JmsSerializerParser extends Parser
{
    /**
     * @var JMS\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $inputString
     * @return Qafoo\ChangeTrack\Analyzer\Result
     */
    public function parseAnalysisResult($inputString)
    {
        return $this->serializer->deserialize(
            $inputString,
            'Qafoo\\ChangeTrack\\Analyzer\\Result',
            'xml'
        );
    }
}

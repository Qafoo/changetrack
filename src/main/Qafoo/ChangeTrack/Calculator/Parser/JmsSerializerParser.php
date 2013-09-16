<?php

namespace Qafoo\ChangeTrack\Calculator\Parser;

use Qafoo\ChangeTrack\Calculator\Parser;

class JmsSerializerParser extends Parser
{
    /**
     * @param string $inputString
     * @return Qafoo\ChangeTrack\Analyzer\Result
     */
    public function parseAnalysisResult($inputString)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/../../../../../config/jmsserializer', 'Qafoo\\ChangeTrack')
            ->build();

        return $serializer->deserialize(
            $inputString,
            'Qafoo\\ChangeTrack\\Analyzer\\Result',
            'xml'
        );
    }
}

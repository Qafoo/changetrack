<?php

namespace Qafoo\ChangeTrack;

class JmsSerializerFactory
{
    /**
     * @var string
     */
    private $metaDataDir;

    /**
     * @param string $metaDataDir
     */
    public function __construct($metaDataDir)
    {
        $this->metaDataDir = $metaDataDir;
    }

    /**
     * @return JMS\Serializer\SerializerInterface
     */
    public function createSerializer()
    {
        return \JMS\Serializer\SerializerBuilder::create()
            ->addMetadataDir($this->metaDataDir, 'Qafoo\\ChangeTrack')
            ->build();
    }
}

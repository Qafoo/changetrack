<?php

namespace Qafoo\ChangeTrack\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AnonymouseServiceGenerator
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * @var int
     */
    private $runningNumber = 0;

    /**
     * @var string
     */
    private $seed;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $definition
     * @return \Symfony\Component\DependencyInjection\Reference Reference to the anonymous service
     */
    public function registerAnonymousService(Definition $definition)
    {
        $serviceId = $this->generateUniqueServiceId($definition->getClass());
        $this->container->setDefinition($serviceId, $definition);
        return new Reference($serviceId);
    }

    /**
     * Generates a service ID somewhat unique and speaking for our project.
     *
     * @param string $serviceClass
     * @return string
     */
    private function generateUniqueServiceId($serviceClass)
    {
        return sprintf(
            '__%s.%s.%s',
            strtr($serviceClass, '\\', '.'),
            $this->getSeed(),
            ++$this->runningNumber
        );
    }

    /**
     * Returns a random seed to make service IDs unique across instances.
     *
     * @return string
     */
    private function getSeed()
    {
        if (!isset($this->seed)) {
            $this->seed = substr(md5(microtime()), 0, 5);
        }
        return $this->seed;
    }
}

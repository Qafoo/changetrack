<?php

namespace Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder;

use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder;
use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\BuilderDispatcher;

use Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator;
use Symfony\Component\DependencyInjection\Definition;

class ChainSelectionLabelProviderBuilder extends LabelProviderBuilder
{
    /**
     * @var \Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator
     */
    private $serviceGenerator;

    /**
     * @param \Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator $serviceGenerator
     */
    public function __construct(AnonymouseServiceGenerator $serviceGenerator)
    {
        $this->serviceGenerator = $serviceGenerator;
    }

    /**
     * @param array $providerConfig
     * @param \Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\BuilderDispatcher $builderDispatcher
     */
    public function buildProvider(array $providerConfig, BuilderDispatcher $builderDispatcher)
    {
        $chainedProviders = array();

        foreach ($providerConfig as $innerConfig) {
            $innerProviderConfig = reset($innerConfig);
            $innerProviderIdentifier = key($innerConfig);

            $chainedProviders[] = $builderDispatcher->dispatchBuilding(
                $innerProviderIdentifier,
                $innerProviderConfig
            );
        }

        return $this->serviceGenerator->registerAnonymousService(
            new Definition(
                'Qafoo\\ChangeTrack\\Calculator\\StatsCollector\\RevisionLabelProvider\\ChainSelectionLabelProvider',
                array('chainedLabelProviders' => $chainedProviders)
            )
        );
    }
}

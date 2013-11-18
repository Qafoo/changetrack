<?php

namespace Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension;

use Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator;
use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder\ChainSelectionLabelProviderBuilder;
use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder\RegexLabelProviderBuilder;
use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder\GithubIssueLabelProviderBuilder;
use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder\DefaultLabelProviderBuilder;

class BuilderDispatcher
{
    /**
     * @var \Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator
     */
    private $serviceGenerator;

    /**
     * @pram \Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator $serviceGenerator
     * @var array(string => \Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder)
     */
    private $builderMap;

    public function __construct(AnonymouseServiceGenerator $serviceGenerator)
    {
        $this->builderMap = array(
            'chain' => new ChainSelectionLabelProviderBuilder($serviceGenerator),
            'regex' => new RegexLabelProviderBuilder($serviceGenerator),
            'github' => new GithubIssueLabelProviderBuilder($serviceGenerator),
            'default' => new DefaultLabelProviderBuilder($serviceGenerator)
        );
    }

    /**
     * Dispatches building of a provider identified by $providerIdentifier
     * using $providerConfig.
     *
     * @param string $providerIdentifier
     * @param array $providerConfig
     */
    public function dispatchBuilding($providerIdentifier, array $providerConfig)
    {
        if (!isset($this->builderMap[$providerIdentifier])) {
            throw new \RuntimeException(
                sprintf(
                    'Invalid label provider identifier "%s", allowed are: %s',
                    $providerIdentifier,
                    implode(', ', array_keys($this->builderMap))
                )
            );
        }

        return $this->builderMap[$providerIdentifier]->buildProvider(
            $providerConfig,
            $this
        );
    }
}

<?php

namespace Qafoo\ChangeTrack\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\BuilderDispatcher;
use Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator;

class RevisionLabelProviderExtension implements ExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = $configs[0];

        $baseConfig = reset($configs);
        $baseIdentifier = key($configs);

        $builderDispatcher = new BuilderDispatcher(
            new AnonymouseServiceGenerator($container)
        );

        $generatedServiceReference = $builderDispatcher->dispatchBuilding($baseIdentifier, $baseConfig);

        $container->setAlias(
            'Qafoo.ChangeTrack.Calculator.RevisionLabelProvider',
            (string) $generatedServiceReference
        );
    }

    public function getAlias()
    {
        return 'revision_label_provider';
    }

    public function getXsdValidationBasePath()
    {
        return false;
    }

    public function getNamespace()
    {
        return false;
    }
}

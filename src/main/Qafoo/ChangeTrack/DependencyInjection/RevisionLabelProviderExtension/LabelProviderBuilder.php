<?php

namespace Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension;

use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\BuilderDispatcher;

abstract class LabelProviderBuilder
{
    /**
     * @param array $providerConfig
     * @param \Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\BuilderDispatcher $builderDispatcher
     */
    abstract public function buildProvider(array $providerConfig, BuilderDispatcher $builderDispatcher);
}

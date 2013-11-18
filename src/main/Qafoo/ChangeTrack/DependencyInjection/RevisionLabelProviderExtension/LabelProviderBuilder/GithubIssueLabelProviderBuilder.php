<?php

namespace Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder;

use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\LabelProviderBuilder;
use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension\BuilderDispatcher;

use Qafoo\ChangeTrack\DependencyInjection\AnonymouseServiceGenerator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class GithubIssueLabelProviderBuilder extends LabelProviderBuilder
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
        return $this->serviceGenerator->registerAnonymousService(
            new Definition(
                'Qafoo\\ChangeTrack\\Calculator\\RevisionLabelProvider\\GithubIssueLabelProvider',
                array(
                    new Reference('Qafoo.ChangeTrack.HttpClient'),
                    $providerConfig['issue_url_template'],
                    $providerConfig['label_map']
                )
            )
        );
    }
}

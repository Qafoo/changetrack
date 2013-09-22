<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension;

class Bootstrap
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var string
     */
    private $configDir;

    /**
     * @var string
     */
    private $configFile;

    public function __construct()
    {
        $this->baseDir = __DIR__ . '/../../../..';
        $this->configDir = $this->baseDir . '/src/config';
        $this->configFile = $this->configDir . '/config.yml.dist';
    }

    /**
     * @param string $configFile
     */
    public function setConfigFile($configFile)
    {
        $this->configFile = $configFile;
    }

    /**
     * @return \Symfony\DependencyInjection\Container
     */
    public function createContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('Qafoo.ChangeTrack.BaseDir', $this->baseDir);

        $container->registerExtension(new RevisionLabelProviderExtension());

        $loader = new XmlFileLoader($container, new FileLocator($this->configDir));
        $loader->load('services.xml');

        $loader = new YamlFileLoader($container, new FileLocator(getcwd()));
        $loader->load($this->configFile);

        // Compile container to make extensions hook in
        $container->compile();

        return $container;
    }
}

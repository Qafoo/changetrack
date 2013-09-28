<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Qafoo\ChangeTrack\DependencyInjection\RevisionLabelProviderExtension;

use Qafoo\ChangeTrack\Application;
use Qafoo\ChangeTrack\Commands;

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
     * @return \Qafoo\ChangeTrack\Application
     */
    public function createApplication()
    {
        $container = $this->createContainer();

        $application = new Application('Qafoo ChangeTrack');
        $application->add(new Commands\Analyze($container));
        $application->add(new Commands\Calculate($container));

        return $application;
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

        return $container;
    }
}

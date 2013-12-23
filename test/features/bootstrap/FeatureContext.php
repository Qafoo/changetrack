<?php

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

use Qafoo\ChangeTrack\Bootstrap;
use Qafoo\ChangeTrack\RepositoryFactory;

require __DIR__ . '/../../../vendor/autoload.php';

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $bootstrap = new Bootstrap();
        $this->container = $bootstrap->createContainer();

        $this->container->setParameter(
            'Qafoo.ChangeTrack.Analyzer.WorkingPath',
            __DIR__ . '/../../../src/var/tmp'
        );

        $this->container->compile();

        $this->useContext('analyzer', new AnalyzerContext($this->container));
        $this->useContext('calculator', new CalculatorContext($this->container));
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result
     */
    public function getAnalyzedChanges()
    {
        return $this->getMainContext()->getSubContext('analyzer')->getAnalyzedChanges();
    }
}

<?php

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

use Qafoo\ChangeTrack\Bootstrap;
use Qafoo\ChangeTrack\RepositoryFactory;

use Qafoo\ChangeTrack\Analyzer\ResultBuilder;
use Qafoo\ChangeTrack\FISCalculator;

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

    /**
     * Creates a fake result from a table.
     *
     * @Given /^I have analyzed the following changes$/
     */
    public function iHaveAnalyzedTheFollowingChanges(TableNode $table)
    {
        $resultBuilder = new ResultBuilder('irrelevant-repository');

        foreach ($table->getHash() as $row) {
            $methodChanges = $resultBuilder->revisionChanges($row['Revision'])
                ->packageChanges($row['Package'])
                ->classChanges($row['Class'])
                ->methodChanges($row['Method']);

            if ((int) $row['Added'] > 0) {
                $methodChanges->lineAdded();
            }
            if ((int) $row['Removed'] > 0) {
                $methodChanges->lineRemoved();
            }
        }

        $this->analyzedChanges = $resultBuilder->buildResult();
    }

    /**
     * @When /^I calculate frequent item sets with min support "([^"]*)"$/
     */
    public function iCalculateFrequentItemSetsWithMinSupport($minSupport)
    {
        $minSupport = (float) $minSupport;
        $calculator = new FISCalculator();
        $this->frequentItemSets = $calculator->calculateFrequentItemSets($this->analyzedChanges, $minSupport);

    }

    /**
     * @Then /^I have the following frequent item sets$/
     */
    public function iHaveTheFollowingFrequentItemSets(TableNode $table)
    {
        throw new PendingException();
    }
}

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
     * @var \Qafoo\ChangeTrack\Calculator\Stats
     */
    private $calculatedStats;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $repositoryUrlOverride;

    /**
     * @var string
     */
    private $checkoutDir;

    /**
     * @var string
     */
    private $cacheDir;

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
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result
     */
    public function getAnalyzedChanges()
    {
        return $this->getMainContext()->getSubContext('analyzer')->getAnalyzedChanges();
    }

    /**
     * @When /^I calculate the stats$/
     */
    public function iCalculateTheStats()
    {
        $calculator = $this->container->get('Qafoo.ChangeTrack.Calculator');
        $this->calculatedStats = $calculator->calculateStats($this->getAnalyzedChanges());
    }

    /**
     * @Then /^I have the following stats$/
     */
    public function iHaveTheFollowingStats(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $package = $row['Package'];
            $class = $row['Class'];
            $method = $row['Method'];
            $changeType = $row['Change Type'];
            $value = (int) $row['Value'];

            if (!isset($this->calculatedStats->packageStats[$package]->classStats[$class]->methodStats[$method]->labelStats[$changeType])) {
                throw new \RuntimeException(
                    sprintf(
                        'No stats found for change type "%s", class "%s" and method "%s"',
                        $changeType,
                        $class,
                        $method
                    )
                );
            }
            if ($this->calculatedStats->packageStats[$package]->classStats[$class]->methodStats[$method]->labelStats[$changeType] != $value) {
                throw new \RuntimeException(
                    sprintf(
                        'Stats value for change type "%s", class "%s" and method "%s" is %s, expected %s',
                        $changeType,
                        $class,
                        $method,
                        $this->calculatedStats->packageStats[$package]->classStats[$class]->methodStats[$method]->labelStats[$changeType],
                        $value
                    )
                );
            }
        }
    }
}

<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Qafoo\ChangeTrack\Bootstrap;
use Qafoo\ChangeTrack\Analyzer;
use Qafoo\ChangeTrack\Calculator;
use Qafoo\ChangeTrack\RepositoryFactory;
use Qafoo\ChangeTrack\Analyzer\RevisionBoundaries;
use Qafoo\ChangeTrack\Analyzer\PathFilter;

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
     * @var Qafoo\ChangeTrack\Analyzes
     */
    private $analyzer;

    private $calculatedStats;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $repositoryUrl;

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
    }

    /**
     * @Given /^I have the repository$/
     */
    public function iHaveTheRepository()
    {
        $repositoryFactory = new RepositoryFactory();
        $this->repositoryUrl = $repositoryFactory->getRepositoryUrl();
    }

    /**
     * @When /^I analyze the changes$/
     */
    public function iAnalyzeTheChanges()
    {
        $this->iAnalyzeTheChangesFromTo(null, null);
    }

    /**
     * @When /^I analyze the changes of paths "([^"]*)"$/
     */
    public function iAnalyzeTheChangesOfPaths($pathsStr)
    {
        $paths = preg_split('/\s*,\s*/', $pathsStr);

        $this->iAnalyzeTheChangesFromTo(null, null, $paths);
    }

    /**
     * @When /^I analyze the changes from "([^"]*)" to "([^"]*)"$/
     */
    public function iAnalyzeTheChangesFromTo($startRevision, $endRevision, array $paths = array())
    {
        $analyzer = $this->container->get('Qafoo.ChangeTrack.Analyzer');

        $this->analyzedChanges = $analyzer->analyze(
            $this->getRepositoryUrl(),
            new RevisionBoundaries($startRevision, $endRevision),
            new PathFilter($paths)
        );
    }

    /**
     * @Then /^there are no stats for revision "([^"]*)"$/
     */
    public function thereAreNoStatsForRevision($revision)
    {
        if (isset($this->analyzedChanges->revisionChanges[$revision])) {
            throw new \RuntimeException(
                sprintf(
                    'Unexpected analysis result for revision "%s"',
                    $revision
                )
            );
        }
    }

    private function getRepositoryUrl()
    {
        return $this->repositoryUrl;
    }

    /**
     * @Then /^there are the following stats in revision "([^"]*)"$/
     */
    public function thereAreTheFollowingStatsInRevision($revision, TableNode $table)
    {
        foreach ($table->getHash() as $rows) {
            $package = $rows['Package'];
            $class = $rows['Class'];
            $method = $rows['Method'];
            $added = $rows['Added'];
            $removed = $rows['Removed'];

            if (!isset($this->analyzedChanges->revisionChanges[$revision])) {
                throw new \RuntimeException(
                    sprintf(
                        'Revision %s not found in stats.',
                        $revision
                    )
                );
            }
            if (!isset($this->analyzedChanges->revisionChanges[$revision]->packageChanges[$package])) {
                throw new \RuntimeException(
                    sprintf(
                        'Package %s not found in stats for revision %s.',
                        $package,
                        $revision
                    )
                );
            }
            if (!isset($this->analyzedChanges->revisionChanges[$revision]->packageChanges[$package]->classChanges[$class])) {
                throw new \RuntimeException(
                    sprintf(
                        'Class %s from package %s not found in stats for revision %s.',
                        $class,
                        $package,
                        $revision
                    )
                );
            }
            if (!isset($this->analyzedChanges->revisionChanges[$revision]->packageChanges[$package]->classChanges[$class]->methodChanges[$method])) {
                throw new \RuntimeException(
                    sprintf(
                        'Method %s\%s::%s() not found in stats for revision %s.',
                        $package,
                        $class,
                        $method,
                        $revision
                    )
                );
            }
            if ($this->analyzedChanges->revisionChanges[$revision]->packageChanges[$package]->classChanges[$class]->methodChanges[$method]->numLinesAdded != $added) {
                throw new \RuntimeException(
                    sprintf(
                        'Added stats for %s\%s::%s() incorrect for revision %s. Expected: %s. Actual: %s',
                        $package,
                        $class,
                        $method,
                        $revision,
                        $added,
                        $this->analyzedChanges[$revision][$package][$class][$method]->numLinesAdded
                    )
                );
            }
            if ($this->analyzedChanges->revisionChanges[$revision]->packageChanges[$package]->classChanges[$class]->methodChanges[$method]->numLinesRemoved != $removed) {
                throw new \RuntimeException(
                    sprintf(
                        'Removed stats for %s\%s::%s() incorrect for revision %s. Expected: %s. Actual: %s',
                        $package,
                        $class,
                        $method,
                        $revision,
                        $added,
                        $this->analyzedChanges[$revision][$package][$class][$method]->numLinesRemoved
                    )
                );
            }
        }
    }

    /**
     * @When /^I calculate the stats$/
     */
    public function iCalculateTheStats()
    {
        $calculator = $this->container->get('Qafoo.ChangeTrack.Calculator');
        $this->calculatedStats = $calculator->calculateStats($this->analyzedChanges);
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

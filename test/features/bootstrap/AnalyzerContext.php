<?php

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Qafoo\ChangeTrack\RepositoryFactory;
use Qafoo\ChangeTrack\Analyzer\RevisionBoundaries;
use Qafoo\ChangeTrack\Analyzer\PathFilter;

class AnalyzerContext extends BehatContext
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer
     */
    private $analyzer;

    /**
     * @var string
     */
    private $repositoryUrl;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Result
     */
    private $analyzedChanges;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result
     * @throws \RuntimeException if no changes were analyzes
     */
    public function getAnalyzedChanges()
    {
        if ($this->analyzedChanges === null) {
            throw new \RuntimeException('No changes analyzed, yet.');
        }
        return $this->analyzedChanges;
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

    private function getRepositoryUrl()
    {
        return $this->repositoryUrl;
    }
}

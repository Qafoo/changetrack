<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Qafoo\ChangeTrack\Analyzer;
use Qafoo\ChangeTrack\Change;

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

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^I have the repository "([^"]*)"$/
     */
    public function iHaveTheRepository($repositoryUrl)
    {
        $checkoutDir = __DIR__ . '/../../../src/var/tmp/checkout';
        $cacheDir = __DIR__ . '/../../../src/var/tmp/cache';

        $this->cleanupDirectory($checkoutDir);
        $this->cleanupDirectory($cacheDir);

        $this->analyzer = new Analyzer(
            $repositoryUrl,
            $checkoutDir,
            $cacheDir
        );
    }

    /**
     * @When /^I analyze the changes$/
     */
    public function iAnalyzeTheChanges()
    {
        $this->analyzesChanges = $this->analyzer->analyze();
    }

    /**
     * @Then /^there are the following stats in revision "([^"]*)"$/
     */
    public function thereAreTheFollowingStatsInRevision($revision, TableNode $table)
    {
        foreach ($table->getHash() as $rows) {
            $class = $rows['Class'];
            $method = $rows['Method'];
            $added = $rows['Added'];
            $removed = $rows['Removed'];

            if (!isset($this->analyzesChanges[$revision])) {
                throw new \RuntimeException(
                    sprintf(
                        'Revision %s not found in stats.',
                        $revision
                    )
                );
            }
            if (!isset($this->analyzesChanges[$revision][$class])) {
                throw new \RuntimeException(
                    sprintf(
                        'Class %s not found in stats for revision %s.',
                        $class,
                        $revision
                    )
                );
            }
            if (!isset($this->analyzesChanges[$revision][$class][$method])) {
                throw new \RuntimeException(
                    sprintf(
                        'Method %s::%s() not found in stats for revision %s.',
                        $class,
                        $method,
                        $revision
                    )
                );
            }
            if ($this->analyzesChanges[$revision][$class][$method][Change::ADDED] != $added) {
                throw new \RuntimeException(
                    sprintf(
                        'Added stats for %s::%s() incorrect for revision %s. Expected: %s. Actual: %s',
                        $class,
                        $method,
                        $revision,
                        $added,
                        $this->analyzesChanges[$revision][$class][$method][Change::ADDED]
                    )
                );
            }
            if ($this->analyzesChanges[$revision][$class][$method][Change::REMOVED] != $removed) {
                throw new \RuntimeException(
                    sprintf(
                        'Removed stats for %s::%s() incorrect for revision %s. Expected: %s. Actual: %s',
                        $class,
                        $method,
                        $revision,
                        $added,
                        $this->analyzesChanges[$revision][$class][$method][Change::ADDED]
                    )
                );
            }
        }
    }

    protected function cleanupDirectory($directory)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory,
                \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path => $fileSystemNode) {
            if ($fileSystemNode->isDir()) {
                rmdir($path);
            } else {
                unlink($path);
            }
        }

        if (!is_dir($directory)) {
            mkdir($directory);
        }
    }
}

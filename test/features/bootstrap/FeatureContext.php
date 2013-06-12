<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Qafoo\ChangeTrack\Analyzer;

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
     * @Then /^I have a count of "([^"]*)" for method "([^"]*)" in class "([^"]*)"$/
     */
    public function iHaveACountOfForMethodInClass($expectedChangeCount, $methodName, $className)
    {
        if (!isset($this->analyzesChanges[$className])) {
            throw new \RuntimeException("Class $className not found.");
        }
        if (!isset($this->analyzesChanges[$className][$methodName])) {
            throw new \RuntimeException("Method $methodName in class $className not found.");
        }

        $actualChangeCount = $this->analyzesChanges[$className][$methodName];
        if ($actualChangeCount != $expectedChangeCount) {
            throw new \RuntimeException("Count for method $methodName in class $className is $actualChangeCount, expected $expectedChangeCount.");
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

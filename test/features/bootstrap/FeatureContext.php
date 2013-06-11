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
        $this->analyzer = new Analyzer($repositoryUrl);
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
}

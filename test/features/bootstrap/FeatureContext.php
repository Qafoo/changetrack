<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

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
    public function iHaveTheRepository($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When /^I analyze the changes$/
     */
    public function iAnalyzeTheChanges()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I have a count of "([^"]*)" for method "([^"]*)" in class "([^"]*)"$/
     */
    public function iHaveACountOfForMethodInClass($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }
}

<?php

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CalculatorContext extends BehatContext
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Qafoo\ChangeTrack\Calculator\Stats
     */
    private $calculatedStats;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result
     */
    public function getAnalyzedChanges()
    {
        return $this->getMainContext()->getAnalyzedChanges();
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

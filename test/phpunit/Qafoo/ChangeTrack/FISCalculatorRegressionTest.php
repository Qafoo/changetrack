<?php

namespace Qafoo\ChangeTrack;

class FISCalculatorRegressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Qafoo\ChangeTrack\TestTools
     */
    private $testTools;

    public function setUp()
    {
        $this->testTools = new TestTools();

        $this->resultFile = $this->testTools->getApplicationPath('test/temp_result.xml');
        // Overwrite parent to disable it to disable
    }

    public function tearDown()
    {
        unlink($this->resultFile);
    }

    public function testCalculatorRegression()
    {
        $command = $this->testTools->getApplicationPath('src/bin/track');
        $inputFile = $this->testTools->getApplicationPath('test/phpunit/_fixtures/regression_analysis_daemon.xml');
        $resultFile = $this->resultFile;

        `$command frequent-item-sets -s '0.2' $inputFile > $resultFile`;

        $this->assertXmlStringEqualsXmlString(
            $this->loadExpectedXml('method'),
            file_get_contents($resultFile)
        );
    }

    /*
    public function testCalculatorRegressionWithClasses()
    {
        $command = $this->testTools->getApplicationPath('src/bin/track');
        $inputFile = $this->testTools->getApplicationPath('test/phpunit/_fixtures/regression_analysis_daemon.xml');
        $resultFile = $this->resultFile;

        `$command frequent-item-sets -s '0.2 -i class' $inputFile > $resultFile`;

        $this->assertXmlStringEqualsXmlString(
            $this->loadExpectedXml(),
            file_get_contents($resultFile)
        );
    }
    */

    /**
     * @param string $repositoryUrl
     * @return string
     */
    private function loadExpectedXml($suffix)
    {
        $referenceXml = file_get_contents(
            $this->testTools->getApplicationPath(
                sprintf(
                    'test/phpunit/_fixtures/regression_fiscalculate_daemon_%s.xml',
                    $suffix
                )
            )
        );

        return $referenceXml;
    }
}

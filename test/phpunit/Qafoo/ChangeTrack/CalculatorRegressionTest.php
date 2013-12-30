<?php

namespace Qafoo\ChangeTrack;

/**
 * @group regression
 */
class AnalyzerRegressionTest extends CheckoutAwareTestBase
{
    /**
     * @var \Qafoo\ChangeTrack\TestTools
     */
    private $testTools;

    private $resultFile;

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

    public function testAnalyzerRegressionDaemonRepository()
    {
        $repositoryUrl = $this->getRepositoryUrl();
        $resultFile = $this->resultFile;

        $command = $this->testTools->getApplicationPath('src/bin/track');
        $workingDir = $this->testTools->getApplicationPath('src/var/tmp');

        `$command analyze -w $workingDir -o $resultFile -v $repositoryUrl`;

        $this->assertXmlStringEqualsXmlString(
            $this->loadExpectedXml($repositoryUrl),
            file_get_contents($resultFile)
        );
    }

    /**
     * @param string $repositoryUrl
     * @return string
     */
    private function loadExpectedXml($repositoryUrl)
    {
        $referenceXml = file_get_contents(
            __DIR__ . '/../../_fixtures/regression_analysis_daemon.xml'
        );

        return str_replace('{repoUrl}', $repositoryUrl, $referenceXml);
    }
}

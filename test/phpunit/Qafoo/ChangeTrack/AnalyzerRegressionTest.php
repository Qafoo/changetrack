<?php

namespace Qafoo\ChangeTrack;

/**
 * @group regression
 */
class AnalyzerRegressionTest extends CheckoutAwareTestBase
{

    private $resultFile;

    private $applicationBasePath;

    public function setUp()
    {
        $this->applicationBasePath = __DIR__ . '/../../../../';
        $this->resultFile = $this->applicationBasePath . '/test/temp_result.xml';
        // Overwrite to disable
    }

    public function tearDown()
    {
        unlink($this->resultFile);
    }

    public function testAnalyzerRegressionDaemonRepository()
    {
        $repositoryUrl = $this->getRepositoryUrl();
        $resultFile = $this->resultFile;

        $command = $this->applicationBasePath . '/src/bin/track';
        $workingDir = $this->applicationBasePath . '/src/var/tmp';

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

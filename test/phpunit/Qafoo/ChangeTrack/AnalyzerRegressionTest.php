<?php

namespace Qafoo\ChangeTrack;

/**
 * @group regression
 */
class AnalyzerRegressionTest extends CheckoutAwareTestBase
{

    private $resultFile = 'test/temp_result.xml';

    public function setUp()
    {
        `rm -rf src/var/tmp/*`;
    }

    public function tearDown()
    {
        unlink('test/temp_result.xml');
        `rm -rf src/var/tmp/*`;
    }

    public function testAnalyzerRegressionDaemonRepository()
    {
        $repositoryUrl = $this->getRepositoryUrl();
        $resultFile = $this->resultFile;

        `src/bin/track analyze -o $resultFile -v $repositoryUrl`;

        $expectedXml = $this->loadExpectedXml($repositoryUrl);

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

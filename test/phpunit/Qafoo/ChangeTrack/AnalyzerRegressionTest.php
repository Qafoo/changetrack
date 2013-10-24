<?php

namespace Qafoo\ChangeTrack;

/**
 * @group regression
 */
class AnalyzerRegressionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        `rm -rf src/var/tmp/checkout`;
        `rm -rf src/var/tmp/cache`;
        `mkdir src/var/tmp/checkout`;
        `mkdir src/var/tmp/cache`;
    }

    public function tearDown()
    {
        unlink('test/temp_result.xml');
    }

    public function testAnalyzerRegressionDaemonRepository()
    {
        `src/bin/track analyze -o test/temp_result.xml  https://github.com/QafooLabs/Daemon.git`;

        $this->assertXmlFileEqualsXmlFile(
            __DIR__ . '/../../_fixtures/regression_analysis_daemon.xml',
            'test/temp_result.xml'
        );
    }
}

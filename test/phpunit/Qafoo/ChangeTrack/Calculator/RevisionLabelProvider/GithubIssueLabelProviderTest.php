<?php

namespace Qafoo\ChangeTrack\Calculator\RevisionLabelProvider;

use Qafoo\ChangeTrack\HttpClient;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class GithubIssueLabelProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Qafoo\ChangeTrack\HttpClient
     */
    private $httpClientMock;

    private $labelMap = array('bug' => 'fix');

    public function setUp()
    {
        $gitHubLabel = new \stdClass();
        $gitHubLabel->name = 'bug';

        $this->defaultLabelResponse = new HttpClient\Response(
            200,
            json_encode(array($gitHubLabel))
        );

        $this->httpClientMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\HttpClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpClientMock->expects($this->any())
            ->method('get')
            ->will(
                $this->returnValue($this->defaultLabelResponse)
            );
    }

    public function testProvideLabelCallsCorrectUrl()
    {
        $labelProvider = $this->createLabelProvider();

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('https://api.github.com/repos/vendor/project/issues/23/labels'))
            ->will($this->returnValue($this->defaultLabelResponse));

        $labelProvider->provideLabel(
            new RevisionChanges('abcd', 'Fixed #23 finally.', array())
        );
    }

    public function testProvideLabelReturnsMappedLabel()
    {
        $labelProvider = $this->createLabelProvider();

        $actualLabel = $labelProvider->provideLabel(
            new RevisionChanges('abcd', 'Fixed #23 finally.', array())
        );

        $this->assertEquals('fix', $actualLabel);
    }

    private function createLabelProvider()
    {
        return new GithubIssueLabelProvider(
            $this->httpClientMock,
            'https://api.github.com/repos/vendor/project/issues/:id/labels',
            $this->labelMap
        );
    }
}

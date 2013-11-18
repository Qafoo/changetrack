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

    private $labelMap;

    public function setUp()
    {
        $gitHubLabel = new \stdClass();
        $gitHubLabel->name = 'bug';

        $this->defaultLabelResponse = new HttpClient\Response(
            200,
            json_encode(array($gitHubLabel))
        );

        $this->labelMap = array('bug' => 'fix', 'feature' => 'implement');


        $this->httpClientMock = $this->createHttpClientMock();

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

    public function testProvideLabelReturnsOtherMappedLabel()
    {
        $gitHubLabel = new \stdClass();
        $gitHubLabel->name = 'feature';

        $labelResponse = new HttpClient\Response(
            200,
            json_encode(array($gitHubLabel))
        );

        $this->httpClientMock = $this->createHttpClientMock();
        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($labelResponse));

        $labelProvider = $this->createLabelProvider();

        $actualLabel = $labelProvider->provideLabel(
            new RevisionChanges('abcd', 'Fixed #23 finally.', array())
        );

        $this->assertEquals('implement', $actualLabel);
    }

    public function testCanProvideLabelIfAvailable()
    {
        $labelProvider = $this->createLabelProvider();

        $this->assertTrue(
            $labelProvider->canProvideLabel(
                new RevisionChanges('abcd', 'Fixed #23 finally.', array())
            )
        );
    }

    public function testCanProvideLabelThrowsExceptionIfGithubFails()
    {
        $this->httpClientMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\HttpClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpClientMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(new HttpClient\Response(500)));

        $labelProvider = $this->createLabelProvider();

        $this->setExpectedException('\\RuntimeException');

        $labelProvider->canProvideLabel(
            new RevisionChanges('abcd', 'Fixed #23 finally.', array())
        );
    }

    public function testCanProvideLabelThrowsExceptionIfGithubReturnsInvalidJSON()
    {
        $this->httpClientMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\HttpClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpClientMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(new HttpClient\Response(500)));

        $labelProvider = $this->createLabelProvider();

        $this->setExpectedException('\\RuntimeException');

        $labelProvider->canProvideLabel(
            new RevisionChanges('abcd', 'Fixed #23 finally.', array())
        );
    }

    public function testCannotProvideLabelIfNoReferenceInCommitMessage()
    {
        $labelProvider = $this->createLabelProvider();

        $this->assertFalse(
            $labelProvider->canProvideLabel(
                new RevisionChanges('abcd', 'Fixed no reference.', array())
            )
        );
    }

    private function createLabelProvider()
    {
        return new GithubIssueLabelProvider(
            $this->httpClientMock,
            'https://api.github.com/repos/vendor/project/issues/:id/labels',
            $this->labelMap
        );
    }

    private function createHttpClientMock()
    {
        return $this->getMockBuilder('Qafoo\\ChangeTrack\\HttpClient')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

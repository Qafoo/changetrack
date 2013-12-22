<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Reflection\ReflectionException;

class ChangeRecorderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ResultBuilder
     */
    private $resultBuilderMock;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ReflectionLookup
     */
    private $reflectionLookupMock;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeRecorder
     */
    private $changeRecorder;

    public function setUp()
    {
        $this->resultBuilderMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\Analyzer\\ResultBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->reflectionLookupMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\Analyzer\\ReflectionLookup')
            ->disableOriginalConstructor()
            ->getMock();

        $this->changeRecorder = new ChangeRecorder($this->resultBuilderMock, $this->reflectionLookupMock);
    }

    public function testReflectionExceptionEaten()
    {
        $this->reflectionLookupMock->expects($this->any())
            ->method('determineAffectedMethod')
            ->will($this->throwException(new ReflectionException('foo.php')));

        $this->changeRecorder->recordChange(
            $this->getNullChange(),
            $this->getCheckoutMock()
        );

        // No exception should be thrown
    }

    /**
     * @return \Qafoo\ChangeTrack\Change
     */
    private function getNullChange()
    {
        return new Change(
            new Change\LocalChange(
                new Change\FileChange(null, null),
                new Change\LineAddedChange(null)
            ),
            null,
            null
        );
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout
     */
    private function getCheckoutMock()
    {
        return $this->getMockBuilder('Qafoo\\ChangeTrack\\Analyzer\\Vcs\\GitCheckout')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

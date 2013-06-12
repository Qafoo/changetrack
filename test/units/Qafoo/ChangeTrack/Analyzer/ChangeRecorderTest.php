<?php

namespace Qafoo\ChangeTrack\Analyzer;

class ChangeRecorderTest extends \PHPUnit_Framework_TestCase
{
    public function testRecordChange()
    {
        $changeRecorder = new ChangeRecorder('abc', 'Message');

        $reflectionClass = new \ReflectionClass(__CLASS__);
        $reflectionMethod = $reflectionClass->getMethod(__FUNCTION__);

        $changeRecorder->recordChange($reflectionClass, $reflectionMethod);

        $this->assertEquals(
            array(
                __CLASS__ => array(
                    __FUNCTION__ => 1
                )
            ),
            $changeRecorder->getChanges()
        );
    }

    public function testRecordChangeDouble()
    {
        $changeRecorder = new ChangeRecorder('abc', 'Message');

        $reflectionClass = new \ReflectionClass(__CLASS__);
        $reflectionMethod = $reflectionClass->getMethod(__FUNCTION__);

        $changeRecorder->recordChange($reflectionClass, $reflectionMethod);
        $changeRecorder->recordChange($reflectionClass, $reflectionMethod);

        $this->assertEquals(
            array(
                __CLASS__ => array(
                    __FUNCTION__ => 1
                )
            ),
            $changeRecorder->getChanges()
        );
    }

    public function testRecordMultipleChanges()
    {
        $changeRecorder = new ChangeRecorder('abc', 'Message');

        $testClass = new \ReflectionClass(__CLASS__);
        $testMethod = $testClass->getMethod(__FUNCTION__);

        $productionClass = new \ReflectionClass('Qafoo\\ChangeTrack\\Analyzer\\ChangeRecorder');
        $productionMethod = $productionClass->getMethod('recordChange');

        $changeRecorder->recordChange($testClass, $testMethod);
        $changeRecorder->recordChange($productionClass, $productionMethod);

        $this->assertEquals(
            array(
                __CLASS__ => array(
                    __FUNCTION__ => 1
                ),
                'Qafoo\\ChangeTrack\\Analyzer\\ChangeRecorder' => array(
                    'recordChange' => 1
                )
            ),
            $changeRecorder->getChanges()
        );
    }
}

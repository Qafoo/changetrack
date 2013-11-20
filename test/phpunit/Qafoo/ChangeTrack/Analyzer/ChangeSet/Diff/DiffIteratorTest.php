<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Arbit\VCSWrapper\Diff;

class DiffIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteratesOnlyPhpFiles()
    {
        $diffParser = new Diff\Unified();
        $diffCollection = $diffParser->parseString(
            file_get_contents(
                __DIR__ . '/../../../../../_fixtures/diff_with_different_file_types.diff'
            )
        );

        $diffIterator = new DiffIterator($diffCollection);

        $containedFiles = array();
        foreach ($diffIterator as $change) {
            $containedFiles[$change->getFileChange()->getFromFile()] = true;
            $containedFiles[$change->getFileChange()->getToFile()] = true;
        }

        $this->assertFalse(
            isset($containedFiles['src/config/services.xml'])
        );
    }
}

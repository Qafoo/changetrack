<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Arbit\VCSWrapper\Diff;
use Qafoo\ChangeTrack\Analyzer\Checkout\VcsWrapperDiffMapper;

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
        $diffMapper = new VcsWrapperDiffMapper();
        $diffs = $diffMapper->mapDiffs($diffCollection);

        $diffIterator = new DiffIterator($diffs);

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

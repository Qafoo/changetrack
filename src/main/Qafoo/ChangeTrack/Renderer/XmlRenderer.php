<?php

namespace Qafoo\ChangeTrack\Renderer;

use Qafoo\ChangeTrack\Renderer;
use Qafoo\ChangeTrack\Change;
use Qafoo\ChangeTrack\Analyzer\Result;

class XmlRenderer extends Renderer
{
    const URN = 'urn:com.qafoo.qachangetrack.changes';

    const SHORTCUT = 'qac';

    /**
     * Render the output of $analysisResult into a string and return it.
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Result $analysisResult
     * @return string
     */
    public function renderOutput(Result $analysisResult)
    {
        $domDocument = new \DOMDocument();
        $domDocument->formatOutput = true;

        $rootElement = $this->createElement($domDocument, 'changes');

        foreach ($analysisResult as $revisionChanges) {
            $changeSetElement = $rootElement->appendChild(
                $domDocument->createElementNS(self::URN, 'changeSet')
            );
            $changeSetElement->setAttributeNS(self::URN, 'revision', $revisionChanges->revision);
            $changeSetElement->setAttributeNS(self::URN, 'message', $revisionChanges->commitMessage);

            foreach ($revisionChanges as $classChanges) {
                $classElement = $changeSetElement->appendChild(
                    $domDocument->createElementNS(self::URN, 'class')
                );
                $classElement->setAttributeNS(self::URN, 'name', $classChanges->className);

                foreach ($classChanges as $methodChange) {
                    $methodElement = $classElement->appendChild(
                        $domDocument->createElementNS(self::URN, 'method')
                    );
                    $methodElement->setAttributeNS(self::URN, 'name', $methodChange->methodName);

                    $methodElement->appendChild(
                        $domDocument->createElementNS(self::URN, 'added', $methodChange->numLinesAdded)
                    );
                    $methodElement->appendChild(
                        $domDocument->createElementNS(self::URN, 'removed', $methodChange->numLinesRemoved)
                    );
                }
            }
        }
        return $domDocument->saveXml();
    }

    private function createElement(\DOMNode $parent, $localName)
    {
        $ownerDocument = ($parent instanceof \DOMDocument) ? $parent : $parent->ownerDocument;

        return $parent->appendChild(
            $ownerDocument->createElementNS(
                self::URN,
                sprintf('%s:%s', self::SHORTCUT, $localName)
            )
        );
    }
}

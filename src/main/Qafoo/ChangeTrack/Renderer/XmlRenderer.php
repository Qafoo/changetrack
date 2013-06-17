<?php

namespace Qafoo\ChangeTrack\Renderer;

use Qafoo\ChangeTrack\Renderer;
use Qafoo\ChangeTrack\Change;

class XmlRenderer extends Renderer
{
    const URN = 'urn:com.qafoo.qachangetrack.changes';

    const SHORTCUT = 'qac';

    /**
     * Render the output of $analysisResult into a string and return it.
     *
     * @param array $analysisResult
     * @return string
     */
    public function renderOutput(array $analysisResult)
    {
        $domDocument = new \DOMDocument();
        $domDocument->formatOutput = true;

        $rootElement = $this->createElement($domDocument, 'changes');

        foreach ($analysisResult as $revision => $classChanges) {
            $changeSetElement = $rootElement->appendChild(
                $domDocument->createElementNS(self::URN, 'changeSet')
            );
            $changeSetElement->setAttributeNS(self::URN, 'revision', $revision);

            foreach ($classChanges as $className => $methodChanges) {
                $classElement = $changeSetElement->appendChild(
                    $domDocument->createElementNS(self::URN, 'class')
                );
                $classElement->setAttributeNS(self::URN, 'name', $className);

                foreach ($methodChanges as $methodName => $actionStats) {
                    $methodElement = $classElement->appendChild(
                        $domDocument->createElementNS(self::URN, 'method')
                    );
                    $methodElement->setAttributeNS(self::URN, 'name', $methodName);

                    $methodElement->appendChild(
                        $domDocument->createElementNS(self::URN, 'added', $actionStats[Change::ADDED])
                    );
                    $methodElement->appendChild(
                        $domDocument->createElementNS(self::URN, 'removed', $actionStats[Change::REMOVED])
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

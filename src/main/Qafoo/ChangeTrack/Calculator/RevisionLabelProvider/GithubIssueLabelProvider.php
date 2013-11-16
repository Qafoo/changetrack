<?php

namespace Qafoo\ChangeTrack\Calculator\RevisionLabelProvider;

use Qafoo\ChangeTrack\Calculator\RevisionLabelProvider;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;
use Qafoo\ChangeTrack\HttpClient;

class GithubIssueLabelProvider implements RevisionLabelProvider
{
    /**
     * @var \Qafoo\ChangeTrack\HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $issueUrlTemplate;

    /**
     * @var array
     */
    private $labelMap;

    /**
     * @param \Qafoo\ChangeTrack\HttpClient $httpclient
     * @param string $issueUrlTemplate
     * @param array $labelMap
     */
    public function __construct(HttpClient $httpClient, $issueUrlTemplate, array $labelMap)
    {
        $this->httpClient = $httpClient;
        $this->issueUrlTemplate = $issueUrlTemplate;
        $this->labelMap = $labelMap;
    }

    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return string
     */
    public function provideLabel(RevisionChanges $revisionChanges)
    {
        if (preg_match('(#([0-9]+))', $revisionChanges->commitMessage, $matches) < 1) {
            throw new \RuntimeException(
                sprintf(
                    'No issue reference found in commit message "%s" of revision "%s"',
                    $revisionChanges->commitMessage,
                    $revisionChanges->revision
                )
            );
        }
        $issueId = $matches[1];

        $url = str_replace(':id', $issueId, $this->issueUrlTemplate);

        $response = $this->httpClient->get($url);
        if ($response->status !== 200) {
            throw new \RuntimeException(
                sprintf(
                    'Response code of GET "%s" was "%s" expected "200"',
                    $url,
                    $response->status
                )
            );
        }

        $labels = json_decode($response->body);

        if ($labels === null) {
            throw new \RuntimeException(
                sprintf('Retrieved no valid JSON from "%s"', $url)
            );
        }

        foreach ($labels as $label) {
            if (isset($this->labelMap[$label->name])) {
                return $this->labelMap[$label->name];
            }
        }

        throw new \RuntimeException(
            'No mapping label found for issue "%s". Issue labels were: "%s"',
            $issueId,
            implode(
                '", "',
                array_map(
                    function ($labelData) {
                        return $labelData->name;
                    },
                    $labels
                )
            )
        );
    }

    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return bool
     */
    public function canProvideLabel(RevisionChanges $revisionChanges)
    {
        throw new \RuntimeException("Not implemented, yet.");
    }
}

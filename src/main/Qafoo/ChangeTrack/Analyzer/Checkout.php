<?php

namespace Qafoo\ChangeTrack\Analyzer;

interface Checkout
{
    /**
     * Updates the checkout to $version
     *
     * If $version is null, the checkout should be updated to HEAD.
     *
     * @param string $version
     */
    public function update($version = null);

    /**
     * Returns the diff that was applied in the given $revision
     *
     * @param string $revision
     * @return array(\Arbit\VCSWrapper\Diff\Collection)
     *
     * @todo Get rid of hard VCSWrapper dependency.
     */
    public function getRevisionDiff($revision);

    /**
     * Returns the local path of the checkout root.
     *
     * @return string
     */
    public function getLocalPath();

    /**
     * Returns the predecessor revision of $revision or null, if non exists.
     *
     * @param string $revision
     * @return string|null
     */
    public function getPredecessor($revision);

    /**
     * Returns a list of all revisions in the checkout.
     *
     * @return string[]
     */
    public function getVersions();

    /**
     * Returns the log entry for the given $revision.
     *
     * @param string $revision
     * @return \Arbit\VCSWrapper\LogEntry
     *
     * @todo Get rid of hard VCSWrapper dependency.
     */
    public function getLogEntry($revision);
}

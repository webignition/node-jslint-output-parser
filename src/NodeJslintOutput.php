<?php

namespace webignition\NodeJslintOutput;

use webignition\NodeJslintOutput\Entry\Entry;

/**
 * Models the output from nodejs-lint
 *
 * Output comprises a collection of entries. Each entry comprises a header line
 * and a fragment line.
 *
 * Example output with 3 entries:
 * /home/example/example.js
 *  #1 Unexpected '(space)'.
 *     application.progress.testController = function () { // Line 4, Pos 52
 *  #2 Unexpected '(space)'.
 *     // Line 6, Pos 1
 *  #3 Combine this with the previous 'var' statement.
 *     var setCompletionPercentValue = function () { // Line 7, Pos 9
 *
 */
class NodeJslintOutput
{
    /**
     * @var string
     */
    private $statusLine = null;

    /**
     * @var array
     */
    private $entries = [];

    /**
     * @param string $statusLine
     */
    public function setStatusLine($statusLine)
    {
        $this->statusLine = $statusLine;
    }

    /**
     * @return string
     */
    public function getStatusLine()
    {
        return $this->statusLine;
    }

    /**
     * @return int
     */
    public function getEntryCount()
    {
        return count($this->entries);
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param Entry $entry
     */
    public function addEntry(Entry $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * @return bool
     */
    public function wasStopped()
    {
        foreach ($this->entries as $entry) {
            if ($this->isStoppingEntry($entry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasTooManyErrors()
    {
        foreach ($this->entries as $entry) {
            if ($this->isTooManyErrorsEntry($entry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getPercentScanned()
    {
        foreach ($this->entries as $entry) {
            /* @var $entry Entry */


            if ($this->isStoppingEntry($entry)) {
                $percentStringMatches = array();
                preg_match("/[0-9]{1,3}\%/", $entry->getReason(), $percentStringMatches);
                return str_replace('%', '', $percentStringMatches[0]);
            }

            if ($this->isTooManyErrorsEntry($entry)) {
                $percentStringMatches = array();
                preg_match("/[0-9]{1,2}\%/", $entry->getReason(), $percentStringMatches);
                return str_replace('%', '', $percentStringMatches[0]);
            }
        }

        return 100;
    }

    /**
     * @param Entry $entry
     *
     * @return bool
     */
    private function isStoppingEntry(Entry $entry)
    {
        return preg_match("/Stopping\.\s*\([0-9]{1,3}\% scanned\)\./", $entry->getReason()) > 0;
    }

    /**
     * @param Entry $entry
     *
     * @return bool
     */
    private function isTooManyErrorsEntry(Entry $entry)
    {
        return preg_match("/Too many errors\. \([0-9]{1,2}\% scanned\)\./", $entry->getReason()) > 0;
    }
}

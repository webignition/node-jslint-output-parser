<?php

namespace webignition\NodeJslintOutput;

class DecodedRawOutput
{
    /**
     * @var array
     */
    private $decodedRawOutput;

    /**
     * @param string $rawOutput
     */
    public function __construct($rawOutput)
    {
        $this->decodedRawOutput = RawOutputDecoder::decode($rawOutput);
    }

    /**
     * @return string|null
     */
    public function getStatusLine()
    {
        if (!$this->isWellFormed()) {
            return null;
        }

        return $this->decodedRawOutput[0];
    }

    /**
     * @return array|null
     */
    public function getLintResult()
    {
        if (!$this->isWellFormed()) {
            return null;
        }

        return $this->decodedRawOutput[1];
    }

    /**
     * Is the decoded output of the format we expect?
     * Should be a two-element array with item zero being the path
     * of the file that was linted and the item one being the result set
     *
     * @return bool
     */
    public function isWellFormed()
    {
        if (!is_array($this->decodedRawOutput)) {
            return false;
        }

        if (!isset($this->decodedRawOutput[0]) || !is_string($this->decodedRawOutput[0])) {
            return false;
        }

        if (!isset($this->decodedRawOutput[1]) || !is_array($this->decodedRawOutput[1])) {
            return false;
        }

        return true;
    }
}

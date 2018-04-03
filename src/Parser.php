<?php

namespace webignition\NodeJslintOutput;

use webignition\NodeJslintOutput\Exception as NodeJsLintOutputException;
use webignition\NodeJslintOutput\Entry\Factory as EntryFactory;

/**
 * Parses the output from nodejs-lint
 *
 * Output comprises a collection of entries. Each entry comprises a header line
 * and a fragment line.
 *
 * Example output with 3 entries:
 *  #1 Unexpected '(space)'.
 *     application.progress.testController = function () { // Line 4, Pos 52
 *  #2 Unexpected '(space)'.
 *     // Line 6, Pos 1
 *  #3 Combine this with the previous 'var' statement.
 */
class Parser
{
    const INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN = '/Error: ENOENT, open \'[^\']+\'/';
    const INCORRECT_NODE_JS_PATH_EXCEPTION_MARKER_PATTERN = '/Error: Cannot find module \'[^\']+\'/';
    const EXPECTED_NODE_JSLINT_OUTPUT_OBJECT_COUNT = 2;

    /**
     * @var string
     */
    private $rawOutput = null;

    /**
     * @var mixed
     */
    private $decodedRawOutput = null;

    /**
     * @var NodeJslintOutput
     */
    private $nodeJsLintOutput = null;

    /**
     * @param string $rawOutput
     *
     * @return NodeJslintOutput|bool
     *
     * @throws Entry\ParserException
     * @throws Exception
     */
    public function parse($rawOutput)
    {
        if (!is_string($rawOutput)) {
            return false;
        }

        $this->rawOutput = trim($rawOutput);
        if ($this->isInputFileNotFoundException()) {
            throw $this->createInputFileNotFoundException();
        }

        if ($this->isIncorrectNodeJsPathException()) {
            throw $this->getIncorrectNodeJsPathException();
        }

        if (!$this->isDecodedOutputFormatCorrect()) {
            throw new NodeJsLintOutputException(
                'Unexpected output; is not a lint result set',
                NodeJsLintOutputException::CODE_UNEXPECTED_OUTPUT
            );
        }

        $decodedRawOutput = $this->getDecodedRawOutput();

        $statusLine = $decodedRawOutput[0];
        $entries = $decodedRawOutput[1];

        $this->nodeJsLintOutput = new NodeJslintOutput();
        $this->nodeJsLintOutput->setStatusLine($statusLine);

        if (count($entries) === 0) {
            return $this->nodeJsLintOutput;
        }

        $entryParser = new EntryFactory();

        foreach ($entries as $entryData) {
            if (!is_null($entryData)) {
                $this->nodeJsLintOutput->addEntry($entryParser->create($entryData));
            }
        }

        return $this->nodeJsLintOutput;
    }

    /**
     * Is the decoded output of the format we expect?
     * Should be a two-element array with the 0th item being the path
     * of the file that was linted and the 1st item being the result set
     *
     * @return bool
     */
    private function isDecodedOutputFormatCorrect()
    {
        if (!is_array($this->getDecodedRawOutput())) {
            return false;
        }

        $decodedRawOutput = $this->getDecodedRawOutput();
        if (!is_string($decodedRawOutput[0])) {
            return false;
        }

        if (!is_array($decodedRawOutput[1])) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isInputFileNotFoundException()
    {
        if (!$this->isException()) {
            return false;
        }

        return preg_match(self::INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN, $this->rawOutput) > 0;
    }

    /**
     * @return bool
     */
    private function isIncorrectNodeJsPathException()
    {
        if (!$this->isException()) {
            return false;
        }

        return preg_match(self::INCORRECT_NODE_JS_PATH_EXCEPTION_MARKER_PATTERN, $this->rawOutput) > 0;
    }

    /**
     * @return Exception
     */
    private function createInputFileNotFoundException()
    {
        $path = null;
        $rawOutputLines = explode("\n", $this->rawOutput);

        foreach ($rawOutputLines as $rawOutputLine) {
            if (preg_match(self::INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN, $rawOutputLine)) {
                $firstQuotePosition = strpos($rawOutputLine, "'");
                $lastQuotePosition = strrpos($rawOutputLine, "'");
                $length = $lastQuotePosition - $firstQuotePosition - 1;
                $path = substr($rawOutputLine, $firstQuotePosition + 1, $length);
            }
        }

        return new NodeJsLintOutputException(
            'Input file "' . $path . '" not found',
            Exception::CODE_INPUT_FILE_NOT_FOUND
        );
    }

    /**
     * @return Exception
     */
    private function getIncorrectNodeJsPathException()
    {
        $path = null;
        $rawOutputLines = explode("\n", $this->rawOutput);

        foreach ($rawOutputLines as $rawOutputLine) {
            if (preg_match(self::INCORRECT_NODE_JS_PATH_EXCEPTION_MARKER_PATTERN, $rawOutputLine)) {
                $firstQuotePosition = strpos($rawOutputLine, "'");
                $lastQuotePosition = strrpos($rawOutputLine, "'");
                $length = $lastQuotePosition - $firstQuotePosition - 1;
                $path = substr($rawOutputLine, $firstQuotePosition + 1, $length);
            }
        }

        return new NodeJsLintOutputException(
            'node-jslint not found at "' . $path . '"',
            Exception::CODE_INCORRECT_NODE_JS_PATH
        );
    }

    /**
     *
     * @return bool
     */
    private function isException()
    {
        if ($this->isDecodedOutputFormatCorrect()) {
            return false;
        }

        return substr_count($this->rawOutput, 'throw err;') > 0;
    }

    /**
     * @return \stdClass|null
     */
    private function getDecodedRawOutput()
    {
        if (is_null($this->decodedRawOutput)) {
            $this->decodedRawOutput = json_decode($this->rawOutput, true);
        }

        return $this->decodedRawOutput;
    }
}

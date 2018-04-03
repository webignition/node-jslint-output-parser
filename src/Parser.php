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
    private $rawOutput;

    /**
     * @var DecodedRawOutput
     */
    private $decodedRawOutput;

    /**
     * @var NodeJslintOutput
     */
    private $nodeJsLintOutput;

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

        $rawOutput = trim($rawOutput);

        $this->rawOutput = $rawOutput;
        $this->decodedRawOutput = new DecodedRawOutput(trim($rawOutput));

        $exceptionFactory = new ExceptionFactory($rawOutput);

        if ($exceptionFactory->isInputFileNotFoundException()) {
            throw $exceptionFactory->createInputFileNotFoundException();
        }

        if ($exceptionFactory->isIncorrectNodeJsPathException()) {
            throw $exceptionFactory->createIncorrectNodeJsPathException();
        }

        if (!$this->decodedRawOutput->isWellFormed()) {
            throw new NodeJsLintOutputException(
                'Unexpected output; is not a lint result set',
                NodeJsLintOutputException::CODE_UNEXPECTED_OUTPUT
            );
        }

        $statusLine = $this->decodedRawOutput->getStatusLine();
        $entries = $this->decodedRawOutput->getLintResult();

        $this->nodeJsLintOutput = new NodeJslintOutput();
        $this->nodeJsLintOutput->setStatusLine($statusLine);

        if (empty($entries)) {
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
}

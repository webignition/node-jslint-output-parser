<?php

namespace webignition\NodeJslintOutput;

class ExceptionFactory
{
    const INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN = '/Error: ENOENT, open \'[^\']+\'/';
    const INCORRECT_NODE_JS_PATH_EXCEPTION_MARKER_PATTERN = '/Error: Cannot find module \'[^\']+\'/';

    /**
     * @var string
     */
    private $rawOutput;

    /**
     * @param string $rawOutput
     */
    public function __construct($rawOutput)
    {
        $this->rawOutput = $rawOutput;
    }

    /**
     * @return bool
     */
    public function isException()
    {
        return substr_count($this->rawOutput, 'throw err;') > 0;
    }

    /**
     * @return bool
     */
    public function isInputFileNotFoundException()
    {
        if (!$this->isException()) {
            return false;
        }

        return preg_match(self::INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN, $this->rawOutput) > 0;
    }

    /**
     * @return bool
     */
    public function isIncorrectNodeJsPathException()
    {
        if (!$this->isException()) {
            return false;
        }

        return preg_match(self::INCORRECT_NODE_JS_PATH_EXCEPTION_MARKER_PATTERN, $this->rawOutput) > 0;
    }

    /**
     * @return Exception
     */
    public function createInputFileNotFoundException()
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

        return new Exception(
            'Input file "' . $path . '" not found',
            Exception::CODE_INPUT_FILE_NOT_FOUND
        );
    }

    /**
     * @return Exception
     */
    public function createIncorrectNodeJsPathException()
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

        return new Exception(
            'node-jslint not found at "' . $path . '"',
            Exception::CODE_INCORRECT_NODE_JS_PATH
        );
    }
}

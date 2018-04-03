<?php

namespace webignition\NodeJslintOutput;

class Exception extends \Exception
{
    const CODE_INPUT_FILE_NOT_FOUND = 1;
    const CODE_UNEXPECTED_OUTPUT = 2;
    const CODE_INCORRECT_NODE_JS_PATH = 3;

    /**
     * @return bool
     */
    public function isInputFileNotFound()
    {
        return $this->code === self::CODE_INPUT_FILE_NOT_FOUND;
    }

    /**
     * @return bool
     */
    public function isUnexpectedOutput()
    {
        return $this->code === self::CODE_UNEXPECTED_OUTPUT;
    }

    /**
     * @return bool
     */
    public function isIncorrectNodeJsPath()
    {
        return $this->code === self::CODE_INCORRECT_NODE_JS_PATH;
    }
}

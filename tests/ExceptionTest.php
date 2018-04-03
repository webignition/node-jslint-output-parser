<?php

namespace webignition\Tests\NodeJslintOutput;

use webignition\NodeJslintOutput\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInputFileNotFound()
    {
        $exception = new Exception('', Exception::CODE_INPUT_FILE_NOT_FOUND);

        $this->assertTrue($exception->isInputFileNotFound());
    }

    public function testIsUnexpectedOutput()
    {
        $exception = new Exception('', Exception::CODE_UNEXPECTED_OUTPUT);

        $this->assertTrue($exception->isUnexpectedOutput());
    }

    public function testIsIncorrectNodeJsPath()
    {
        $exception = new Exception('', Exception::CODE_INCORRECT_NODE_JS_PATH);

        $this->assertTrue($exception->isIncorrectNodeJsPath());
    }
}
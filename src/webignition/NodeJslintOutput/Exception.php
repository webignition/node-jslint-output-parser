<?php
namespace webignition\NodeJslintOutput;

use \Exception as BaseException;

class Exception extends BaseException {
    
    const CODE_INPUT_FILE_NOT_FOUND = 1;
    const CODE_UNEXPECTED_OUTPUT = 2;
    const CODE_INCORRECT_NODE_JS_PATH = 3;
    
    /**
     * 
     * @return boolean
     */
    public function isInputFileNotFound() {
        return $this->getCode() === self::CODE_INPUT_FILE_NOT_FOUND;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isUnexpectedOutput() {
        return $this->getCode() === self::CODE_UNEXPECTED_OUTPUT;
    }    
    
    /**
     * 
     * @return boolean
     */    
    public function isIncorrectNodeJsPath() {
        return $this->getCode() === self::CODE_INCORRECT_NODE_JS_PATH;
    }
    
}
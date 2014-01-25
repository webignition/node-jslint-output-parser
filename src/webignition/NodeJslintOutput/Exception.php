<?php
namespace webignition\NodeJslintOutput;

use \Exception as BaseException;

class Exception extends BaseException {
    
    const CODE_INPUT_FILE_NOT_FOUND = 1;    
    
    /**
     * 
     * @return boolean
     */
    public function isInputFileNotFound() {
        return $this->getCode() === self::CODE_INPUT_FILE_NOT_FOUND;
    }
    
}
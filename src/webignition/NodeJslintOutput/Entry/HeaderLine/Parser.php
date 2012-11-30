<?php
namespace webignition\NodeJslintOutput\Entry\HeaderLine;

use webignition\StringParser\StringParser;

/**
 *  Parse a raw nodejs-lint entry header line
 * 
 *  Example header line:  #6 Expected '!==' and instead saw '!='.
 */
class Parser extends StringParser {
    
    const STATE_LOCATING_ERROR_NUMBER = 1;
    const STATE_READING_ERROR_NUMBER = 2;
    const STATE_LOCATING_ERROR_MESSAGE = 3;
    const STATE_READING_ERROR_MESSAGE = 4;
    const STATE_COMPLETE = 5;
    const STATE_FAILED = 6;
    
    const ERROR_NUMBER_PREFIX = '#';
    const ERROR_NUMBER_ERROR_MESSAGE_SEPARATOR = ' ';
    
    /**
     *
     * @var string
     */
    private $errorNumber = '';
    
    
    /**
     *
     * @var string
     */
    private $errorMessage = '';
    
    
    /**
     *
     * @var HeaderLine
     */
    private $headerLine = null;    
    
    protected function parseCurrentCharacter() {
        
        switch ($this->getCurrentState()) {
            case self::STATE_UNKNOWN:
                if ($this->getCurrentCharacterPointer() === 0) {
                    $this->setCurrentState(self::STATE_LOCATING_ERROR_NUMBER);
                }

                break;
            
            case self::STATE_LOCATING_ERROR_NUMBER:               
                if ($this->getCurrentCharacter() == self::ERROR_NUMBER_PREFIX) {
                    $this->setCurrentState(self::STATE_READING_ERROR_NUMBER);
                }
                
                $this->incrementCurrentCharacterPointer();             
                
                break;
            
            case self::STATE_READING_ERROR_NUMBER;
                if (ctype_digit($this->getCurrentCharacter())) {
                    $this->errorNumber .= $this->getCurrentCharacter();                    
                }
                
                if ($this->getCurrentCharacter() == self::ERROR_NUMBER_ERROR_MESSAGE_SEPARATOR) {                    
                    if ($this->errorNumber == '') {
                        $this->setCurrentState(self::STATE_FAILED);
                    } else {
                        $this->headerLine = new HeaderLine();
                        $this->headerLine->setErrorNumber($this->errorNumber);
                        $this->errorNumber = null;
                        $this->setCurrentState(self::STATE_LOCATING_ERROR_MESSAGE);                        
                    }
                }
                
                $this->incrementCurrentCharacterPointer();

                break;
                
            case self::STATE_LOCATING_ERROR_MESSAGE;                
                $this->errorMessage .= $this->getCurrentCharacter();
                
                if ($this->isCurrentCharacterLastCharacter()) {
                    $this->headerLine->setErrorMessage($this->errorMessage);
                    $this->errorMessage = null;
                    $this->setCurrentState(self::STATE_COMPLETE);
                }
                
                $this->incrementCurrentCharacterPointer();
                break;
                
            case self::STATE_FAILED:
                $this->stop();
                break;
        }
    }
    
    
    /**
     * 
     * @return HeaderLine
     */
    public function getHeaderLine() {
        if ($this->hasParsedValidHeaderLine()) {
            return $this->headerLine;
        }
        
        return null;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function hasParsedValidHeaderLine() {
        return $this->getCurrentState() == self::STATE_COMPLETE;        
    }
    
    
}
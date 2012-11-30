<?php
namespace webignition\NodeJslintOutput\Entry\HeaderLine;

/**
 * Models the header line from a nodejs-lint output entry
 * 
 * Raw line format:
 * <one or more spaces>#<error number><space><error message>
 * 
 * Example:
 *  #6 Expected '!==' and instead saw '!='. 
 */
class HeaderLine {
    
    /**
     *
     * @var int
     */
    private $errorNumber = null;    
    
    
    /**
     *
     * @var string
     */
    private $errorMessage = null;
    
    
    /**
     * 
     * @param int $errorNumber
     */
    public function setErrorNumber($errorNumber) {
        $this->errorNumber = (int)$errorNumber;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getErrorNumber() {
        return $this->errorNumber;
    }
    
    
    /**
     * 
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }
    
    
    /**
     * 
     * @return string
     */
    public function __toString() {
        return '#'.$this->getErrorNumber().' '.$this->getErrorMessage();
    }
    
}
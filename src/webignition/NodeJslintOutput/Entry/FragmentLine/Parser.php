<?php
namespace webignition\NodeJslintOutput\Entry\FragmentLine;

use webignition\StringParser\StringParser;

/**
 *  Parse a raw nodejs-lint entry fragment line
 * 
 *  Example fragment line:|
 *      if (completionPercentValue.text() != latestTestData.completion_percent) { // Line 10, Pos 43
 */
class Parser extends StringParser {
    
    const STATE_READING_FRAGMENT = 2;
    const STATE_LOCATING_LINE_NUMBER = 3;
    const STATE_READING_LINE_NUMBER = 4;
    const STATE_LOCATING_COLUMN_NUMBER = 5;
    const STATE_READING_COLUMN_NUMBER = 6;      
    const STATE_COMPLETE = 7;
    
    const FRAGMENT_COORDINATES_SEPARATOR = '//';
    const LINE_NUMBER_COLUMN_NUMBER_SEPARATOR = ',';
    
    
    /**
     *
     * @var string
     */
    private $fragment = '';

    
    /**
     *
     * @var int
     */
    private $lineNumber = '';
    
    
    /**
     *
     * @var int
     */
    private $columnNumber = ''; 
    
    
    /**
     *
     * @var FragmentLine
     */
    private $fragmentLine = null;
    
    
    
    protected function parseCurrentCharacter() {        
        switch ($this->getCurrentState()) {
            case self::STATE_UNKNOWN:
                if ($this->getCurrentCharacterPointer() === 0) {
                    $this->setCurrentState(self::STATE_READING_FRAGMENT);
                }

                break;
            
            case self::STATE_READING_FRAGMENT:                
                if ($this->getCurrentCharacter() . $this->getNextCharacter() == self::FRAGMENT_COORDINATES_SEPARATOR) {
                    $this->fragmentLine = new FragmentLine();                    
                    $this->fragmentLine->setFragment(substr($this->fragment, 0, strlen($this->fragment) - 1));
                    $this->fragment = null;
                    
                    $this->setCurrentState(self::STATE_LOCATING_LINE_NUMBER);
                } else {
                    $this->fragment .= $this->getCurrentCharacter();
                }
                
                $this->incrementCurrentCharacterPointer();
                break;
            
            case self::STATE_LOCATING_LINE_NUMBER:                
                if (ctype_digit($this->getCurrentCharacter())) {
                    $this->setCurrentState(self::STATE_READING_LINE_NUMBER);
                } else {
                    $this->incrementCurrentCharacterPointer();
                }                
                
                break;
            
            case self::STATE_READING_LINE_NUMBER:                
                if (ctype_digit($this->getCurrentCharacter())) {
                    $this->lineNumber .= $this->getCurrentCharacter();
                }
                
                if ($this->getCurrentCharacter() == self::LINE_NUMBER_COLUMN_NUMBER_SEPARATOR) {
                    $this->fragmentLine->setLineNumber($this->lineNumber);
                    $this->lineNumber = null;
                    $this->setCurrentState(self::STATE_LOCATING_COLUMN_NUMBER);
                }
                
                $this->incrementCurrentCharacterPointer();                
                
                break;
            
            case self::STATE_LOCATING_COLUMN_NUMBER:
                if (ctype_digit($this->getCurrentCharacter())) {
                    $this->setCurrentState(self::STATE_READING_COLUMN_NUMBER);
                } else {
                    $this->incrementCurrentCharacterPointer();
                }                
                
                break;
            
            case self::STATE_READING_COLUMN_NUMBER:                
                if (ctype_digit($this->getCurrentCharacter())) {
                    $this->columnNumber .= $this->getCurrentCharacter();
                }                
                
                if ($this->isCurrentCharacterLastCharacter()) {
                    $this->fragmentLine->setColumnNumber($this->columnNumber);
                    $this->columnNumber = null;
                    $this->setCurrentState(self::STATE_COMPLETE);
                }
                
                $this->incrementCurrentCharacterPointer();
                
                break;            
        }
    }
    
    
    /**
     * 
     * @return FragmentLine
     */
    public function getFragmentLine() {
        if  ($this->hasParsedValidFragmentLine()) {
            return $this->fragmentLine;
        }
        
        return null;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function hasParsedValidFragmentLine() {
        return $this->getCurrentState() == self::STATE_COMPLETE;        
    }
    
    
}
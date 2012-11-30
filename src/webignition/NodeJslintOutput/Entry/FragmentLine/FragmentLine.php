<?php
namespace webignition\NodeJslintOutput\Entry\FragmentLine;

/**
 * Models the fragment line from a nodejs-lint output entry
 * 
 * Raw line format:
 * <space><space><space><space><fragment><space>//<space>Line<space><line number>,<space>Pos<space><column number>
 * 
 * Example:
 *     if (completionPercentValue.text() != latestTestData.completion_percent) { // Line 10, Pos 43
 */
class FragmentLine {
        
    /**
     *
     * @var string
     */
    private $fragment = null;
    
    
    /**
     *
     * @var int
     */
    private $lineNumber = null;
    
    
    /**
     *
     * @var int
     */
    private $columnNumber = null;
    
    
    /**
     * 
     * @param string $fragment
     */
    public function setFragment($fragment) {
        $this->fragment = $fragment;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getFragment() {
        return $this->fragment;
    }
    
    
    /**
     * 
     * @param int $lineNumber
     */
    public function setLineNumber($lineNumber) {
        $this->lineNumber = $lineNumber;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getLineNumber() {
        return $this->lineNumber;
    }
    
    
    /**
     * 
     * @param int $columnNumber
     */
    public function setColumnNumber($columnNumber) {
        $this->columnNumber = $columnNumber;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getColumnNumber() {
        return $this->columnNumber;
    }
    
    
    /**
     * 
     * @return string
     */
    public function __toString() {
        return '    ' . $this->getFragment().' // Line '.$this->getLineNumber().', Pos '.$this->getColumnNumber();
    }
}
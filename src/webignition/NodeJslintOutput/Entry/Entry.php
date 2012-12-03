<?php
namespace webignition\NodeJslintOutput\Entry;

use webignition\NodeJslintOutput\Entry\HeaderLine\HeaderLine;
use webignition\NodeJslintOutput\Entry\FragmentLine\FragmentLine;

/**
 * An entry in a nodejs-lint output
 * An entry is comprised of a header line and a fragment line
 * 
 * Example raw entry:
 * #3 Combine this with the previous 'var' statement.
 *    var setCompletionPercentValue = function () { // Line 7, Pos 9
 */
class Entry {
    
    /**
     *
     * @var HeaderLine
     */
    private $headerLine = null;
    
    
    /**
     *
     * @var FragmentLine
     */
    private $fragmentLine = null;
    
    
    /**
     * 
     * @param \webignition\NodeJslintOutput\Entry\HeaderLine\HeaderLine $headerLine
     */
    public function setHeaderLine(HeaderLine $headerLine) {
        $this->headerLine = $headerLine;
    }
    
    /**
     * 
     * @return HeaderLine
     */
    public function getHeaderLine() {
        return $this->headerLine;
    }
    
    /**
     * 
     * @param \webignition\NodeJslintOutput\Entry\FragmentLine\FragmentLine $fragmentLine
     */
    public function setFragmentLine(FragmentLine $fragmentLine) {
        $this->fragmentLine = $fragmentLine;
    }
    
    /**
     * 
     * @return FragmentLine
     */
    public function getFragmentLine() {
        return $this->fragmentLine;
    }
    
    
    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getHeaderLine() . "\n" . $this->getFragmentLine();
    }
    
    
    /**
     * 
     * @return string
     */
    public function __toArray() {
        return array(
            'headerLine' => $this->getHeaderLine()->__toArray(),
            'fragmentLine' => $this->getFragmentLine()->__toArray()
        );
    }
    
    
    
}
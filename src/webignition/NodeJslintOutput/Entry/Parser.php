<?php
namespace webignition\NodeJslintOutput\Entry;

use webignition\NodeJslintOutput\Entry\HeaderLine\Parser as HeaderLineParser;
use webignition\NodeJslintOutput\Entry\FragmentLine\Parser as FragmentLineParser;
use webignition\NodeJslintOutput\Entry\HeaderLine\HeaderLine;
use webignition\NodeJslintOutput\Entry\FragmentLine\FragmentLine;

/**
 * #3 Combine this with the previous 'var' statement.
 *    var setCompletionPercentValue = function () { // Line 7, Pos 9
 */
class Parser {
    
    /**
     *
     * @var Entry
     */
    private $entry = null;
    
    
    /**
     * 
     * @param string $rawEntry
     * @return boolean
     */
    public function parse($rawEntry) {        
        if (!is_string($rawEntry)) {
            return false;
        }
        
        $entryLines = explode("\n", trim($rawEntry));        
        
        if (count($entryLines) != 2) {
            return false;
        }
                
        $headerLineParser = new HeaderLineParser();        
        $headerLineParser->parse($entryLines[0]);
        
        if (!$headerLineParser->hasParsedValidHeaderLine()) {
            return false;
        }
        
        $fragmentLineParser = new FragmentLineParser();
        $fragmentLineParser->parse(substr($entryLines[1], 4));          
        
        if (!$fragmentLineParser->hasParsedValidFragmentLine()) {
            return false;
        }        
        
        $this->entry = new \webignition\NodeJslintOutput\Entry\Entry();
        $this->entry->setHeaderLine($headerLineParser->getHeaderLine());
        $this->entry->setFragmentLine($fragmentLineParser->getFragmentLine());
        
        return true;
    }  
    
    
    /**
     * 
     * @return Entry
     */
    public function getEntry() {
        return $this->entry;
    }
    
}
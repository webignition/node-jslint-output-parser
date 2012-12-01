<?php
namespace webignition\NodeJslintOutput;

use webignition\NodeJslintOutput\Entry\Parser as EntryParser;
use webignition\NodeJslintOutput\NodeJslintOutput;
use webignition\NodeJslintOutput\Entry\FragmentLine\Parser as FragmentLineParser;

/**
 * Parses the output from nodejs-lint
 * 
 * Output comprises a collection of entries. Each entry comprises a header line
 * and a fragment line.
 * 
 * Example output with 3 entries:
 *  #1 Unexpected '(space)'.
 *     application.progress.testController = function () { // Line 4, Pos 52
 *  #2 Unexpected '(space)'.
 *     // Line 6, Pos 1
 *  #3 Combine this with the previous 'var' statement.
 *     var setCompletionPercentValue = function () { // Line 7, Pos 9 
 * 
 */
class Parser {
    
    /**
     *
     * @var NodeJslintOutput
     */
    private $nodeJsLintOutput = null;
    
    
    /**
     * 
     * @param string $rawOutput
     * @return boolean
     */
    public function parse($rawOutput) {
        if (!is_string($rawOutput)) {
            return false;
        }        
        
        $rawOutputLines = explode("\n", trim($rawOutput));
        
        if (count($rawOutputLines) == 0) {
            return false;
        }
        
        if ($this->isEntryFragmentLine($rawOutputLines[0])) {
            $statusLine = '';
            $entryLines = $rawOutputLines;
        } else {
            $statusLine = $rawOutputLines[0];
            $entryLines = array_slice($rawOutputLines, 1);
        }
        
        if ($this->isLineOkStatusLine($statusLine)) {
            $this->nodeJsLintOutput = new NodeJslintOutput();
            return true;
        }
        
        $this->nodeJsLintOutput = new NodeJslintOutput();
        $this->nodeJsLintOutput->setStatusLine($statusLine);
        
        $entryParser = new EntryParser();        
        
        $currentRawEntry = '';
        foreach ($entryLines as $entryLineIndex => $entryLine) {
            if ($entryLineIndex % 2 == 0) {
                $currentRawEntry .= $entryLine;
            } else {
                $currentRawEntry .= "\n" . $entryLine;                
                $entryParser->parse($currentRawEntry);                
                $this->nodeJsLintOutput->addEntry($entryParser->getEntry());
                $currentRawEntry = '';                
            }
        }
        
        return true;
    }    
    
    
    /**
     * 
     * @return NodeJslintOutput
     */
    public function getNodeJsLintOutput() {
        return $this->nodeJsLintOutput;
    }
    
    
    /**
     * 
     * @param string $line
     * @return boolean
     */
    private function isEntryFragmentLine($line) {
        $parser = new FragmentLineParser();
        $parser->parse($line);
        
        return $parser->hasParsedValidFragmentLine();
    }
    
    
    /**
     * 
     * @param string $line
     * @return boolean
     */
    private function isLineOkStatusLine($line) {
        if ($this->isEntryFragmentLine($line)) {
            return false;
        }
        
        if ($this->isEntryFragmentLine($line)) {
            return false;
        }
        
        return preg_match('/is OK$/', $line) > 0;
    } 
    
    
}
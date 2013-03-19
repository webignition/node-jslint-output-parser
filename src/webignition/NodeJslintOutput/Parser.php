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
    const EXPECTED_NODE_JSLINT_OUTPUT_OBJECT_COUNT = 2; 
    
    
    /**
     *
     * @var NodeJslintOutput
     */
    private $nodeJsLintOutput = null;
    
    
//    /**
//     *
//     * @var \stdClass
//     */
//    private $nodeJsLintOutputObject = null;
    
    
    /**
     *
     * @var array
     */
    private $nodeJsLintEntries = array();
    
    
    /**
     * 
     * @param string $rawOutput
     * @return boolean
     */
    public function parse($rawOutput) {
        if (!is_string($rawOutput)) {
            return false;
        }
        
        $nodeJsLintOutputObject = json_decode(trim($rawOutput));
        
        if (!is_array($nodeJsLintOutputObject)) {
            return false;
        }
        
        if (count($nodeJsLintOutputObject) !== self::EXPECTED_NODE_JSLINT_OUTPUT_OBJECT_COUNT) {
            return false;
        }
        
        $statusLine = $nodeJsLintOutputObject[0];
        if (!is_string($statusLine)) {
            return false;
        }
        
        if (!is_array($nodeJsLintOutputObject[1])) {
            return false;
        }
        
        $this->nodeJsLintEntries = $nodeJsLintOutputObject[1];
        
        $this->nodeJsLintOutput = new NodeJslintOutput();
        $this->nodeJsLintOutput->setStatusLine($statusLine);
        
        $entries = $nodeJsLintOutputObject[1];
        if (count($entries) === 0) {
            return true;
        }
        
        $entryParser = new EntryParser();
        
        foreach ($entries as $entryObject) {
            $entryParser->parse($entryObject);
            $this->nodeJsLintOutput->addEntry($entryParser->getEntry());
            
            //var_dump($entryObject);
            exit();
        }
        
        var_dump($statusLine, count($entries));
        exit();
        
//        $rawOutputLines = explode("\n", trim($rawOutput));        
//        
//        if (count($rawOutputLines) == 0) {
//            return false;
//        }
//        
//        if ($this->isEntryFragmentLine($rawOutputLines[0])) {
//            $statusLine = '';
//            $entryLines = $rawOutputLines;
//        } else {
//            $statusLine = $rawOutputLines[0];
//            $entryLines = array_slice($rawOutputLines, 1);
//        }        
//        
//        if ($this->isLineOkStatusLine($statusLine)) {
//            $this->nodeJsLintOutput = new NodeJslintOutput();
//            $this->nodeJsLintOutput->setStatusLine($statusLine);
//            return true;
//        }
//        
//        $this->nodeJsLintOutput = new NodeJslintOutput();
//        $this->nodeJsLintOutput->setStatusLine($statusLine);
//        
//        return true;
        
        $entryParser = new EntryParser();        
        
        $currentRawEntry = '';
        foreach ($entryLines as $entryLineIndex => $entryLine) {
            if ($entryLineIndex % 2 == 0) {
                $currentRawEntry .= $entryLine;
            } else {
                $currentRawEntry .= "\n" . $entryLine;                
                $entryParser->parse($currentRawEntry);                
                
                $entry = $entryParser->getEntry();
                
                var_dump($currentRawEntry);
                
                //return;
                $this->nodeJsLintOutput->addEntry($entry);
                $currentRawEntry = '';                
            }
        }
        
        //return;
        
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
    
    
    //private function is
    
    
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
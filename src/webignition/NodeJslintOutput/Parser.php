<?php
namespace webignition\NodeJslintOutput;

use webignition\NodeJslintOutput\Exception as NodeJsLintOutputException;
use webignition\NodeJslintOutput\Entry\Parser as EntryParser;
use webignition\NodeJslintOutput\NodeJslintOutput;

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
    
    const INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN = '/Error: ENOENT, open \'[^\']+\'/';    
    const EXPECTED_NODE_JSLINT_OUTPUT_OBJECT_COUNT = 2; 
    
    /**
     *
     * @var string
     */
    private $rawOutput = null;
    
    
    /**
     *
     * @var mixed
     */
    private $decodedRawOutput = null;
    
    
    /**
     *
     * @var NodeJslintOutput
     */
    private $nodeJsLintOutput = null;
    
    
    /**
     *
     * @var array
     */
    private $nodeJsLintEntries = array();
    
    
    /**
     * 
     * @param string $rawOutput
     * @return boolean
     * @throws \webignition\NodeJslintOutput\Exception
     */
    public function parse($rawOutput) {
        if (!is_string($rawOutput)) {
            return false;
        }
        
        $this->rawOutput = trim($rawOutput);
        if ($this->isInputFileNotFoundException()) {
            throw $this->getInputFileNotFoundException();
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
            if (!is_null($entryObject)) {
                $entryParser->parse($entryObject);
                $this->nodeJsLintOutput->addEntry($entryParser->getEntry());                
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
     * @return boolean
     */
    private function isInputFileNotFoundException() {
        if (!$this->isException()) {
            return false;
        }
        
        return preg_match(self::INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN, $this->rawOutput) > 0;
    }
    
    
    /**
     * 
     * @return \webignition\NodeJslintOutput\Exception
     */
    private function getInputFileNotFoundException() {
        $path = null;
        $rawOutputLines = explode("\n", $this->rawOutput);
        foreach ($rawOutputLines as $rawOutputLine) {
            if (preg_match(self::INPUT_FILE_NOT_FOUND_EXCEPTION_MARKER_PATTERN, $rawOutputLine)) {
                $firstQuotePosition = strpos($rawOutputLine, "'");
                $lastQuotePosition = strrpos($rawOutputLine, "'");
                $length = $lastQuotePosition - $firstQuotePosition - 1;                
                $path = substr($rawOutputLine, $firstQuotePosition + 1, $length);
            }
        }
        
        return new NodeJsLintOutputException('Input file "'.$path.'" not found', Exception::CODE_INPUT_FILE_NOT_FOUND);
    }
    
    
    /**
     * 
     * @return boolean
     */
    private function isException() {
        if ($this->isRawOutputJson()) {
            return false;
        }
        
        return substr_count($this->rawOutput, 'throw err;') > 0;
    }
    
    
    /**
     * 
     * @return boolean
     */
    private function isRawOutputJson() {        
        return !is_null($this->getDecodedRawOutput());
    }
    
    
    /**
     * 
     * @return \stdClass|null
     */
    private function getDecodedRawOutput() {
        if (is_null($this->decodedRawOutput)) {
            $this->decodedRawOutput = json_decode($this->rawOutput);
        }
        
        return $this->decodedRawOutput;
    }
    
    
}
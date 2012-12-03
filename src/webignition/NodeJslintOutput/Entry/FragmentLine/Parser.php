<?php
namespace webignition\NodeJslintOutput\Entry\FragmentLine;

//use webignition\StringParser\StringParser;

/**
 *  Parse a raw nodejs-lint entry fragment line
 * 
 *  Example fragment line:|
 *      if (completionPercentValue.text() != latestTestData.completion_percent) { // Line 10, Pos 43
 */
class Parser {
    
    const FRAGMENT_COORDINATES_SEPARATOR = '//';
    const LINE_NUMBER_COLUMN_NUMBER_SEPARATOR = ',';    
    
    /**
     *
     * @var FragmentLine
     */
    private $fragmentLine = null;
    
    
    /**
     *
     * @var boolean
     */
    private $hasParsedValidFragmentLine = false;
    
    
    /**
     * 
     * @param string $fragmentLine
     * @return boolean
     */
    public function parse($fragmentLine) {
        $fragmentCoordinatesSeparatorPosition = $this->findFragmentCoordinatesSeparatorPosition($fragmentLine);
        
        if (!is_int($fragmentCoordinatesSeparatorPosition)) {
            return false;
        }
        
        $coordinatesParts = explode(self::LINE_NUMBER_COLUMN_NUMBER_SEPARATOR, substr($fragmentLine, $fragmentCoordinatesSeparatorPosition));
        
        if (count($coordinatesParts) != 2) {
            return false;
        }
        
        $lineNumber = str_replace('// Line ', '', $coordinatesParts[0]);
        if (!ctype_digit($lineNumber)) {
            return false;
        }        
        
        $columnNumber = str_replace(' Pos ', '', $coordinatesParts[1]);
        if (!ctype_digit($columnNumber)) {
            return false;
        }
        
        $this->fragmentLine = new FragmentLine();
        $this->fragmentLine->setLineNumber($lineNumber);
        $this->fragmentLine->setColumnNumber($columnNumber);
        $this->fragmentLine->setFragment(substr($fragmentLine, 0, $fragmentCoordinatesSeparatorPosition - 1));
        
        $this->hasParsedValidFragmentLine = true;
        return true;
    }
    
    
    /**
     * 
     * @param string $fragmentLine
     * @return int
     */
    private function findFragmentCoordinatesSeparatorPosition($fragmentLine) {
        $lineLength = strlen($fragmentLine);        
        $currentCharacter = '';
        $previousCharacter = '';
        
        $fragmentCoordinates = '';
        
        for ($characterIndex = $lineLength - 1; $characterIndex >= 0; $characterIndex--) {
            $currentCharacter = $fragmentLine[$characterIndex];
            $fragmentCoordinates = $currentCharacter . $fragmentCoordinates;
            
            if ($characterIndex < $lineLength - 1) {
                $previousCharacter = $fragmentLine[$characterIndex + 1];
            }           
            
            if ($currentCharacter . $previousCharacter == self::FRAGMENT_COORDINATES_SEPARATOR) {
                return $characterIndex;
            }
        }    
        
        return null;
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
        return $this->hasParsedValidFragmentLine;
    }    
}
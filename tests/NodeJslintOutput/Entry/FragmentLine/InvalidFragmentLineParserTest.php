<?php

use webignition\NodeJslintOutput\Entry\FragmentLine\Parser as FragmentLineParser;

class InvalidFragmentLineParserTest extends BaseTest {

    public function testParseBlankString() {        
        $parser = new FragmentLineParser();
        $parser->parse("");
            
        $this->assertFalse($parser->hasParsedValidFragmentLine());
        $this->assertNull($parser->getFragmentLine());
    } 
    
    
    public function testParseLineWithNoLineNumber() {
        $parser = new FragmentLineParser();
        $parser->parse("application.progress.testController = function () { // Line, Pos 52");
            
        $this->assertFalse($parser->hasParsedValidFragmentLine());
        $this->assertNull($parser->getFragmentLine());        
    }
    
    public function testParseLineWithNoColumnNumber() {
        $parser = new FragmentLineParser();
        $parser->parse("application.progress.testController = function () { // Line 4, Pos");
            
        $this->assertFalse($parser->hasParsedValidFragmentLine());
        $this->assertNull($parser->getFragmentLine());        
    }    
}
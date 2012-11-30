<?php

use webignition\NodeJslintOutput\Entry\HeaderLine\Parser as HeaderLineParser;

class InvalidHeaderLineParserTest extends BaseTest {

    public function testParseBlankString() {        
        $parser = new HeaderLineParser();
        $parser->parse("");
            
        $this->assertFalse($parser->hasParsedValidHeaderLine());
        $this->assertNull($parser->getHeaderLine());
    }     
    
    public function testParseLineWithNoErrorNumber() {
        $headerLines = array(
            "",
            "no error number here",
            "#error number marker with no error number",
        ); 
        
        $parser = new HeaderLineParser();
        
        foreach ($headerLines as $headerLine) {
            $parser->parse($headerLine);
            $this->assertFalse($parser->hasParsedValidHeaderLine());
            $this->assertNull($parser->getHeaderLine());                     
        }     
    } 
    
    public function testParseLineWithNoErrorMessage() {
        $headerLines = array(
            "#1",
            "#2 ",
        ); 
        
        $parser = new HeaderLineParser();
        
        foreach ($headerLines as $headerLine) {
            $parser->parse($headerLine);
            $this->assertFalse($parser->hasParsedValidHeaderLine());
            $this->assertNull($parser->getHeaderLine());                     
        }     
    }     
    
}
<?php

use webignition\NodeJslintOutput\Entry\HeaderLine\Parser as HeaderLineParser;

class HeaderLineParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }       
    
    public function testParseErrorNumber() {
        $parser = new HeaderLineParser();
        
        for ($generatedErrorNumber = 0; $generatedErrorNumber <= 100; $generatedErrorNumber++) {            
            $parser->parse("#".$generatedErrorNumber." Error Message Here");
            $headerLine = $parser->getHeaderLine();
            
            $this->assertEquals($generatedErrorNumber, $headerLine->getErrorNumber());
        }
    }    
    
    
    public function testParseErrorMessage() {
        $headerLineErrorMessages = array(
            "Unexpected '(space)'.",
            "Combine this with the previous 'var' statement.",
            "'$' was used before it was defined.",
            "Expected '!==' and instead saw '!='.",
            "Missing space between ':' and 'latestTestData'.",
            "Expected '===' and instead saw '=='.",
            "Move 'var' declarations to the top of the function.",
            "Stopping.  (4% scanned).",
        ); 
        
        $parser = new HeaderLineParser();
        
        foreach ($headerLineErrorMessages as $index => $headerLineErrorMessage) {
            $parser->parse('#'.$index.' '.$headerLineErrorMessage);
            $headerLine = $parser->getHeaderLine();    
            $this->assertEquals($headerLineErrorMessage, $headerLine->getErrorMessage());            
        }
    }     
    
}
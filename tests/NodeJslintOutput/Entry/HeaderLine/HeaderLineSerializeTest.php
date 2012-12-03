<?php

use webignition\NodeJslintOutput\Entry\HeaderLine\Parser as HeaderLineParser;

class HeaderLineSerializeTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    } 
    
    public function testSerializeHeaderLine() {
        $headerLines = array(
            " #1 Unexpected '(space)'.",
            " #2 Unexpected '(space)'.",
            " #3 Combine this with the previous 'var' statement."
        ); 
        
        $serializedHeaderLines = array(
            "{\"errorNumber\":1,\"errorMessage\":\"Unexpected '(space)'.\"}",
            "{\"errorNumber\":2,\"errorMessage\":\"Unexpected '(space)'.\"}",
            "{\"errorNumber\":3,\"errorMessage\":\"Combine this with the previous 'var' statement.\"}"
        );
        
        foreach  ($headerLines as $index => $headerLineString) {
            $parser = new HeaderLineParser();
            $parser->parse($headerLineString);
            $headerLine = $parser->getHeaderLine();
            
            $this->assertEquals($serializedHeaderLines[$index], json_encode($headerLine->__toArray()));
        }
    }   
    
}
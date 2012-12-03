<?php

use webignition\NodeJslintOutput\Entry\Parser as EntryParser;

class EntrySerializeTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }       
    
    public function testSerializeEntry() {        
        $rawEntries = array(
"#1 Unexpected '(space)'.
    application.progress.testController = function () { // Line 4, Pos 52",
"#2 Unexpected '(space)'.
     // Line 6, Pos 1",
"#3 Combine this with the previous 'var' statement.
    var setCompletionPercentValue = function () { // Line 7, Pos 9",
"#4 Unexpected '(space)'.
     // Line 9, Pos 1",
        );       
        
        $serializedEntries = array(
            '{"headerLine":{"errorNumber":1,"errorMessage":"Unexpected \'(space)\'."},"fragmentLine":{"fragment":"application.progress.testController = function () {","lineNumber":"4","columnNumber":"52"}}',
            '{"headerLine":{"errorNumber":2,"errorMessage":"Unexpected \'(space)\'."},"fragmentLine":{"fragment":"","lineNumber":"6","columnNumber":"1"}}',
            '{"headerLine":{"errorNumber":3,"errorMessage":"Combine this with the previous \'var\' statement."},"fragmentLine":{"fragment":"var setCompletionPercentValue = function () {","lineNumber":"7","columnNumber":"9"}}',
            '{"headerLine":{"errorNumber":4,"errorMessage":"Unexpected \'(space)\'."},"fragmentLine":{"fragment":"","lineNumber":"9","columnNumber":"1"}}'
        );
        
        foreach ($rawEntries as $index => $rawEntry) {
            $parser = new EntryParser();
            $parser->parse($rawEntry);

            $this->assertTrue($parser->parse($rawEntry));

            $entry = $parser->getEntry();

            $this->assertEquals($serializedEntries[$index], json_encode($entry->__toArray()));
        }
    }    
    
}
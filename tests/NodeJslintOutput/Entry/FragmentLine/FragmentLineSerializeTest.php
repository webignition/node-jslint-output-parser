<?php

use webignition\NodeJslintOutput\Entry\FragmentLine\Parser as FragmentLineParser;

class FragmentLineSerializeTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    } 
    
    public function testSerializeFragmentLine() {   
        $fragmentLines = array(
            "application.progress.testController = function () { // Line 4, Pos 52",
            " // Line 6, Pos 1",
            "var setCompletionPercentValue = function () { // Line 7, Pos 9"
        );        
        
        $serializedFragmentLines = array(
            '{"fragment":"application.progress.testController = function () {","lineNumber":"4","columnNumber":"52"}',
            '{"fragment":"","lineNumber":"6","columnNumber":"1"}',
            '{"fragment":"var setCompletionPercentValue = function () {","lineNumber":"7","columnNumber":"9"}'
        );
        
        foreach  ($fragmentLines as $index => $fragmentLineString) {
            $parser = new FragmentLineParser();
            $parser->parse($fragmentLineString);
            $fragmentLine = $parser->getFragmentLine();
            
            $this->assertEquals($serializedFragmentLines[$index], json_encode($fragmentLine->__toArray()));
        }
    }   
    
}
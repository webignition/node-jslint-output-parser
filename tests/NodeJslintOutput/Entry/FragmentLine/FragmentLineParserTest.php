<?php

use webignition\NodeJslintOutput\Entry\FragmentLine\Parser as FragmentLineParser;

class FragmentLineParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }       
    
    public function testParseFragment() {        
        $fragments = array(
            "application.progress.testController = function () {",
            " ",
            "var setCompletionPercentValue = function () {",
            "var completionPercentValue = $('#completion-percent-value');",
            " ",
            "if (completionPercentValue.text() != latestTestData.completion_percent) {3",
            "'width':latestTestData.completion_percent + '%'",
            "});                ",
        );        
        
        $parser = new FragmentLineParser();
        
        foreach ($fragments as $index => $fragment) {            
            $parser->parse($fragment.' // Line 4, Pos 52');
            
            $this->assertTrue($parser->hasParsedValidFragmentLine());
            
            $fragmentLine = $parser->getFragmentLine();
            
            $this->assertNotNull($fragmentLine);
            $this->assertEquals($fragment, $fragmentLine->getFragment());
        }
    }  
    
    
    public function testParseLineNumber() {
        $parser = new FragmentLineParser();
        
        for ($generatedLineNumber = 0; $generatedLineNumber < 100; $generatedLineNumber++) {
            $parser->parse("application.progress.testController = function () { // Line ".$generatedLineNumber.", Pos 52");
            $fragmentLine = $parser->getFragmentLine();
            $this->assertEquals($generatedLineNumber, $fragmentLine->getLineNumber());
        }
    }    
    
    
    public function testParseColumnNumber() {
        $parser = new FragmentLineParser();
        
        for ($generatedColumnNumber = 0; $generatedColumnNumber < 100; $generatedColumnNumber++) {
            $parser->parse("application.progress.testController = function () { // Line 4, Pos ".$generatedColumnNumber);
            $fragmentLine = $parser->getFragmentLine();
            $this->assertEquals($generatedColumnNumber, $fragmentLine->getColumnNumber());
        }
    }  
    
    
    public function testParseFragmentLines() {
        $fragments = array(
            "application.progress.testController = function () {",
            "",
            " ",
            "var setCompletionPercentValue = function () {",
            "var completionPercentValue = $('#completion-percent-value');",
            "if (completionPercentValue.text() != latestTestData.completion_percent) {",
            "'width':latestTestData.completion_percent + '%'",
            "});                ",
        );
        $fragmentLineNumbers = array(4,6,17,8,10,15,16,9);
        $fragmentColumnNumbers = array(51,22,1,9,38,43,29,20);
        
        $parser = new FragmentLineParser();
        
        foreach ($fragments as $index => $fragment) {
            $lineNumber = $fragmentLineNumbers[$index];
            $columnNumber = $fragmentColumnNumbers[$index];
            
            $rawFragmentLine = $fragment." // Line ".$lineNumber.", Pos ".$columnNumber;
            
            $parser->parse($rawFragmentLine);
            $fragmentLine = $parser->getFragmentLine();
            
            $this->assertNotNull($fragmentLine);
            $this->assertEquals($fragment, $fragmentLine->getFragment());
            $this->assertEquals($lineNumber, $fragmentLine->getLineNumber());
            $this->assertEquals($columnNumber, $fragmentLine->getColumnNumber());            
        }      
    }    
    
}
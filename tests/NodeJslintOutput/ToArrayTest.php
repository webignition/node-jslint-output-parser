<?php

use webignition\NodeJslintOutput\Parser as Parser;

class ToArrayTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }    
    
    public function testToArrayPartialStoppedScanOutput() {
        $output = $this->getFixture('PartialScanThreeEntries.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals('{"statusLine":"\/home\/example\/source.js","entries":[{"headerLine":{"errorNumber":1,"errorMessage":"Unexpected \'(space)\'."},"fragmentLine":{"fragment":"application.progress.testController = function () {","lineNumber":"4","columnNumber":"52"}},{"headerLine":{"errorNumber":2,"errorMessage":"Unexpected \'(space)\'."},"fragmentLine":{"fragment":"","lineNumber":"6","columnNumber":"1"}},{"headerLine":{"errorNumber":3,"errorMessage":"Combine this with the previous \'var\' statement."},"fragmentLine":{"fragment":"var setCompletionPercentValue = function () {","lineNumber":"7","columnNumber":"9"}}]}', json_encode($nodeJsLintOutput->__toArray()));
    }    
    
    
    public function testToArrayErrorFreeScan() {
        $output = $this->getFixture('ErrorFreeScan.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals('{"statusLine":"\/home\/example\/source.js is OK","entries":[]}', json_encode($nodeJsLintOutput->__toArray()));
    }     
    
}
<?php

use webignition\NodeJslintOutput\Parser as Parser;

class ParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }       
    
    public function testParseErrorFreeOutput() {
        $output = $this->getFixture('ErrorFreeScan.txt');
        
        $parser = new Parser();
        $parser->parse($output);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertNotNull($nodeJsLintOutput);
        $this->assertEquals(0, $nodeJsLintOutput->getEntryCount());
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
    }
    
    
    public function testParsePartialStoppedScanOutput() {
        $output = $this->getFixture('PartialScanStoppedFourPercent.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals(4, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(38, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->wasStopped());
    }     
    
    
    public function testParsePartialTooManyErrorsOutput() {
        $output = $this->getFixture('PartialScanTooManyErrorsNinetyFivePercent.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals(95, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(101, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->hasTooManyErrors());
    }      
    
}
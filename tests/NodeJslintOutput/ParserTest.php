<?php

use webignition\NodeJslintOutput\Parser as Parser;

class ParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }
    
    public function testParseErrorFreeOutput() {
        $output = $this->getFixture('ErrorFreeScan.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertNotNull($nodeJsLintOutput);
        $this->assertEquals(0, $nodeJsLintOutput->getEntryCount());
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
    }
    
    
    public function testParsePartialStoppedScanOutput() {
        $output = $this->getFixture('PartialScanTooManyErrorsSevenPercent.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals(7, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(51, $nodeJsLintOutput->getEntryCount());
        $this->assertFalse($nodeJsLintOutput->wasStopped());
        $this->assertTrue($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
    }
  
    public function testParsePartialTooManyErrorsOutput() {
        $output = $this->getFixture('PartialScanStoppingFiftyPercent.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals(50, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(16, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->wasStopped());
        $this->assertFalse($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());       
    } 
    
    
    public function testParseLargeOutput() {
        $output = $this->getFixture('LargeOutput.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals(87, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(65, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->wasStopped());
        $this->assertFalse($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());        
    } 
    
    public function testParseInvalidControlCharacterLackingParameter() {
        $output = $this->getFixture('InvalidControlCharacterLackingParameter.txt');
        
        $parser = new Parser();        
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        $parser->getNodeJsLintOutput();           
    }
    
    
    public function testParsePartialStoppingOneHundredPercent() {
        $output = $this->getFixture('OneErrorStoppingOneHundredPercent.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
        $this->assertTrue($nodeJsLintOutput->wasStopped());    
    }     
    
    public function testParseStoppingEntryWithAnyNumberOfSpaces() {
        $output = $this->getFixture('OneErrorStoppingPartialPercent.txt');
        
        $parser = new Parser();
        $parseResult = $parser->parse($output);
        
        $this->assertTrue($parseResult);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertEquals(75, $nodeJsLintOutput->getPercentScanned());
        $this->assertTrue($nodeJsLintOutput->wasStopped());    
    }     
    
}
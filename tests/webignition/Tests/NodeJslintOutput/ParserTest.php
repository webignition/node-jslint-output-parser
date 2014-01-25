<?php

namespace webignition\Tests\NodeJslintOutput;

use webignition\NodeJslintOutput\Parser as Parser;

class ParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }
    
    public function testParseErrorFreeOutput() {
        $output = $this->getFixture('ErrorFreeScan.txt');
        
        $parser = new Parser();
        $nodeJsLintOutput = $parser->parse($output);
        
        $this->assertNotNull($nodeJsLintOutput);
        $this->assertEquals(0, $nodeJsLintOutput->getEntryCount());
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
    }
    
    
    public function testParsePartialStoppedScanOutput() {
        $output = $this->getFixture('PartialScanTooManyErrorsSevenPercent.txt');
        
        $parser = new Parser();
        $nodeJsLintOutput = $parser->parse($output);
        
        $this->assertEquals(7, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(51, $nodeJsLintOutput->getEntryCount());
        $this->assertFalse($nodeJsLintOutput->wasStopped());
        $this->assertTrue($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
    }
  
    public function testParsePartialTooManyErrorsOutput() {
        $output = $this->getFixture('PartialScanStoppingFiftyPercent.txt');
        
        $parser = new Parser();
        $nodeJsLintOutput = $parser->parse($output);
        
        $this->assertEquals(50, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(16, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->wasStopped());
        $this->assertFalse($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());       
    } 
    
    
    public function testParseLargeOutput() {
        $output = $this->getFixture('LargeOutput.txt');
        
        $parser = new Parser();
        $nodeJsLintOutput = $parser->parse($output);
        
        $this->assertEquals(87, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(65, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->wasStopped());
        $this->assertFalse($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());        
    } 
    
    public function testParseInvalidControlCharacterLackingParameter() {
        $output = $this->getFixture('InvalidControlCharacterLackingParameter.txt');
        
        $parser = new Parser();        
        $parser->parse($output);    
    }
    
    
    public function testParsePartialStoppingOneHundredPercent() {
        $output = $this->getFixture('OneErrorStoppingOneHundredPercent.txt');
        
        $parser = new Parser();
        $nodeJsLintOutput = $parser->parse($output);
        
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
        $this->assertTrue($nodeJsLintOutput->wasStopped());    
    }     
    
    public function testParseStoppingEntryWithAnyNumberOfSpaces() {
        $output = $this->getFixture('OneErrorStoppingPartialPercent.txt');
        
        $parser = new Parser();
        $nodeJsLintOutput = $parser->parse($output);
        
        $this->assertEquals(75, $nodeJsLintOutput->getPercentScanned());
        $this->assertTrue($nodeJsLintOutput->wasStopped());    
    }  
    
    public function testInputFileNotFound() {
        $this->setExpectedException('webignition\NodeJsLintOutput\Exception', 'Input file "/home/example/script.js" not found', 1);
        
        $output = $this->getFixture('InputFileNotFound.txt');
        
        $parser = new Parser();
        $parser->parse($output);      
    }
    
    
}
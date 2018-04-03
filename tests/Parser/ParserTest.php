<?php

namespace webignition\Tests\NodeJslintOutput\Parser;

use webignition\Tests\NodeJslintOutput\BaseTest;

use webignition\NodeJslintOutput\Parser as Parser;

class ParserTest extends BaseTest {
    
    private $parser;
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
        
        $this->parser = new Parser();
    }
    
    
    private function getOutput() {
        return $this->getFixture(str_replace('test', '', $this->getName())  . '.txt');
    }      
    
    public function testErrorFreeOutput() {        
        $nodeJsLintOutput = $this->parser->parse($this->getOutput());
        
        $this->assertNotNull($nodeJsLintOutput);
        $this->assertEquals(0, $nodeJsLintOutput->getEntryCount());
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
    }
    
    
    public function testPartialScanTooManyErrorsSevenPercentOutput() {
        $nodeJsLintOutput = $this->parser->parse($this->getOutput());
        
        $this->assertEquals(7, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(51, $nodeJsLintOutput->getEntryCount());
        $this->assertFalse($nodeJsLintOutput->wasStopped());
        $this->assertTrue($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
    }
  
    public function testPartialScanStoppingFiftyPercentOutput() {
        $nodeJsLintOutput = $this->parser->parse($this->getOutput());
        
        $this->assertEquals(50, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(16, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->wasStopped());
        $this->assertFalse($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());       
    } 
    
    
    public function testLargeOutput() {        
        $nodeJsLintOutput = $this->parser->parse($this->getOutput());
        
        $this->assertEquals(87, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals(65, $nodeJsLintOutput->getEntryCount());
        $this->assertTrue($nodeJsLintOutput->wasStopped());
        $this->assertFalse($nodeJsLintOutput->hasTooManyErrors());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());        
    } 
    
    public function testInvalidControlCharacterLackingParameter() {     
        $this->parser->parse($this->getOutput());    
    }
    
    
    public function testStoppingOneHundredPercentOutput() {
        $nodeJsLintOutput = $this->parser->parse($this->getOutput());
        
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
        $this->assertTrue($nodeJsLintOutput->wasStopped());    
    }     
    
    public function testStoppingEntryWithSingleSpaceOutput() {
        $nodeJsLintOutput = $this->parser->parse($this->getOutput());
        
        $this->assertEquals(75, $nodeJsLintOutput->getPercentScanned());
        $this->assertTrue($nodeJsLintOutput->wasStopped());    
    }  
}
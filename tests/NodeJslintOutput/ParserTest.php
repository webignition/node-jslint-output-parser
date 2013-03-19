<?php

use webignition\NodeJslintOutput\Parser as Parser;

class ParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }   
    
    public function testParse() {
        $output = $this->getFixture('test.txt');
        
        $parser = new Parser();
        $parser->parse($output);
        
//        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
//        
//        $this->assertNotNull($nodeJsLintOutput);
//        $this->assertEquals(0, $nodeJsLintOutput->getEntryCount());
//        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
//        $this->assertEquals('/home/example/source.js is OK', $nodeJsLintOutput->getStatusLine());
    }
    
    public function testParseErrorFreeOutput() {
        $output = $this->getFixture('ErrorFreeScan.txt');
        
        $parser = new Parser();
        $parser->parse($output);
        
        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
        $this->assertNotNull($nodeJsLintOutput);
        $this->assertEquals(0, $nodeJsLintOutput->getEntryCount());
        $this->assertEquals(100, $nodeJsLintOutput->getPercentScanned());
        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
    }
//    
//    
//    public function testParsePartialStoppedScanOutput() {
//        $output = $this->getFixture('PartialScanStoppedFourPercent.txt');
//        
//        $parser = new Parser();
//        $parseResult = $parser->parse($output);
//        
//        $this->assertTrue($parseResult);
//        
//        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
//        
//        $this->assertEquals(4, $nodeJsLintOutput->getPercentScanned());
//        $this->assertEquals(38, $nodeJsLintOutput->getEntryCount());
//        $this->assertTrue($nodeJsLintOutput->wasStopped());
//        $this->assertEquals('/home/example/source.js', $nodeJsLintOutput->getStatusLine());
//    }     
//    
//    
//    public function testParsePartialTooManyErrorsOutput() {
//        $output = $this->getFixture('PartialScanTooManyErrorsNinetyFivePercent.txt');
//        
//        $parser = new Parser();
//        $parseResult = $parser->parse($output);
//        
//        $this->assertTrue($parseResult);
//        
//        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
//        
//        $this->assertEquals(95, $nodeJsLintOutput->getPercentScanned());
//        $this->assertEquals(101, $nodeJsLintOutput->getEntryCount());
//        $this->assertTrue($nodeJsLintOutput->hasTooManyErrors());
//        $this->assertEquals('/home/example/example.js', $nodeJsLintOutput->getStatusLine());        
//    } 
//    
//    
//    public function testParseLargeOutput() {
//        $output = $this->getFixture('LargeOutput.txt');
//        
//        $parser = new Parser();
//        $parser->parse($output);
//        
//        $nodeJsLintOutput = $parser->getNodeJsLintOutput();
//        
//        $this->assertNotNull($nodeJsLintOutput);
//        $this->assertEquals(51, $nodeJsLintOutput->getEntryCount());
//        $this->assertEquals(50, $nodeJsLintOutput->getPercentScanned());
//        $this->assertEquals('/tmp/3d1ad92fdd42a23f7a6890e9171081c4', $nodeJsLintOutput->getStatusLine());        
//    }    
    
}
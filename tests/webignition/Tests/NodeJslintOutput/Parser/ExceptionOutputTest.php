<?php

namespace webignition\Tests\NodeJslintOutput\Parser;

use webignition\Tests\NodeJslintOutput\BaseTest;

use webignition\NodeJslintOutput\Parser as Parser;

class ExceptionOutputTest extends BaseTest {
    
    private $parser;
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
        $this->parser = new Parser();
    }
    
    public function testInputFileNotFound() {        
        $this->setExpectedException('webignition\NodeJsLintOutput\Exception', 'Input file "/home/example/script.js" not found', 1);        
        $this->parser->parse($this->getOutput());      
    } 
    
    public function testUnexpectedOutput() {        
        $this->setExpectedException('webignition\NodeJsLintOutput\Exception', 'Unexpected output; is not a lint result set', 2);
        $this->parser->parse($this->getOutput());      
    }      
    
    public function testIncorrectNodeJsPathOutput() {        
        $this->setExpectedException('webignition\NodeJsLintOutput\Exception', 'node-jslint not found at "/home/example/node_modules/jslint/bin/jslint.js"', 3);
        $this->parser->parse($this->getOutput());      
    }       
    
    private function getOutput() {
        return $this->getFixture(str_replace('test', '', $this->getName())  . '.txt');
    }    
}
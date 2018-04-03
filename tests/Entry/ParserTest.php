<?php

namespace webignition\Tests\NodeJslintOutput\Entry;

use webignition\NodeJslintOutput\Entry\Parser as EntryParser;
use webignition\Tests\NodeJslintOutput\BaseTest;

class ParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }       
    
    public function testParseEntry() {
        $jsonEntry = '
        {
            "id":"(error)",
            "raw":"Expected \'{a}\' at column {b}, not column {c}.",
            "evidence":"       \"Niets van deze web site mag worden verveelvoudigd en/of \\n\" +",
            "line":3,
            "character":8,
            "a":"Niets van deze web site mag worden verveelvoudigd en/of \n",
            "b":9,
            "c":8,
            "reason":"Expected \'Niets van deze web site mag worden verveelvoudigd en/of \n\' at column 9, not column 8."
        }';
        
        $entryObject = json_decode(trim($jsonEntry));     
        
        $parser = new EntryParser();
        $parser->parse($entryObject);
        
        $this->assertTrue($parser->parse($entryObject));
        
        $entry = $parser->getEntry();
        $this->assertInstanceOf('webignition\NodeJslintOutput\Entry\Entry', $entry);
        
        $this->assertEquals('(error)', $entry->getId());
        $this->assertEquals('Expected \'{a}\' at column {b}, not column {c}.', $entry->getRaw());
        $this->assertEquals("       \"Niets van deze web site mag worden verveelvoudigd en/of \n\" +", $entry->getEvidence());
        $this->assertEquals(3, $entry->getLineNumber());
        $this->assertEquals(8, $entry->getColumnNumber());
        $this->assertEquals(array(
            'a' => "Niets van deze web site mag worden verveelvoudigd en/of \n",
            'b' => 9,
            'c' => 8,
        ), $entry->getParameters());
        $this->assertEquals("Expected 'Niets van deze web site mag worden verveelvoudigd en/of \n' at column 9, not column 8.", $entry->getReason());
    }    
    
}
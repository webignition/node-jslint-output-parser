<?php

use webignition\NodeJslintOutput\Entry\Parser as EntryParser;

class EntryParserTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }       
    
    public function testParseEntry() {
        $rawEntry = " #6 Expected '!==' and instead saw '!='.
    if (completionPercentValue.text() != latestTestData.completion_percent) { // Line 10, Pos 43";
        
        $parser = new EntryParser();
        $parser->parse($rawEntry);
        
        $this->assertTrue($parser->parse($rawEntry));
        
        $entry = $parser->getEntry();
        $this->assertNotNull($entry);
        
        $rawEntryLines = explode("\n", $rawEntry);
        $trimmedRawEntry = trim($rawEntryLines[0]) . "\n" . trim($rawEntryLines[1]);
        
        $this->assertEquals($trimmedRawEntry, (string)$entry);
    }    
    
}
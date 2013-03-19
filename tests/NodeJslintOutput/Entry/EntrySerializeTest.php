<?php

use webignition\NodeJslintOutput\Entry\Parser as EntryParser;
use webignition\NodeJslintOutput\Entry\Serializer as EntrySerializer;

class EntrySerializeTest extends BaseTest {
    
    public function setUp() {
        $this->setTestFixturePath(__CLASS__, $this->getName());
    }       
    
    public function testSerializeSingleEntry() {        
        $jsonEntry = '{"id":"(error)","raw":"Expected \'{a}\' at column {b}, not column {c}.","evidence":"evidence content","line":3,"character":8,"a":"a value","b":9,"c":8,"reason":"Expected \'a value\' at column 9, not column 8."}';
        
        $entryObject = json_decode(trim($jsonEntry));     
        
        $parser = new EntryParser();
        $parser->parse($entryObject);
        
        $serializer = new EntrySerializer();
        
        $this->assertEquals($jsonEntry, $serializer->serialize($parser->getEntry()));       
    } 
    
    public function testSerializeSingleEntryExcludingId() {
        $jsonEntry = '{"id":"(error)","raw":"Expected \'{a}\' at column {b}, not column {c}.","evidence":"evidence content","line":3,"character":8,"a":"a value","b":9,"c":8,"reason":"Expected \'a value\' at column 9, not column 8."}';
        $expectedSerializedEntry = '{"raw":"Expected \'{a}\' at column {b}, not column {c}.","evidence":"evidence content","line":3,"character":8,"a":"a value","b":9,"c":8,"reason":"Expected \'a value\' at column 9, not column 8."}';        
        
        $entryObject = json_decode(trim($jsonEntry));     
        
        $parser = new EntryParser();
        $parser->parse($entryObject);
        
        $serializer = new EntrySerializer();
        
        $this->assertEquals($expectedSerializedEntry, $serializer->serialize($parser->getEntry(), array('id')));
    }
    
    public function testSerializeSingleEntryExcludeParameters() {        
        $jsonEntry = '{"id":"(error)","raw":"Expected \'{a}\' at column {b}, not column {c}.","evidence":"evidence content","line":3,"character":8,"a":"a value","b":9,"c":8,"reason":"Expected \'a value\' at column 9, not column 8."}';
        $expectedSerializedEntry = '{"id":"(error)","raw":"Expected \'{a}\' at column {b}, not column {c}.","evidence":"evidence content","line":3,"character":8,"reason":"Expected \'a value\' at column 9, not column 8."}';
        
        $entryObject = json_decode(trim($jsonEntry));     
        
        $parser = new EntryParser();
        $parser->parse($entryObject);
        
        $serializer = new EntrySerializer();
        $serializer->setExcludeParameters(true);
        
        $this->assertEquals($expectedSerializedEntry, $serializer->serialize($parser->getEntry()));       
    }     
    
}
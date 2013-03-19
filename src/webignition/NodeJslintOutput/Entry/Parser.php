<?php
namespace webignition\NodeJslintOutput\Entry;

use webignition\NodeJslintOutput\Entry\Entry;

class Parser {    
    const ID_PROPERTY_NAME = 'id';
    const RAw_PROPERTY_NAME = 'raw';
    const EVIDENCE_PROPERTY_NAME = 'evidence';
    const LINE_PROPERTY_NAME = 'line';
    const CHARACTER_PROPERTY_NAME = 'character';
    const REASON_PROPERTY_NAME = 'reason';
    
    /**
     * Collection of required property names
     * 
     * @var array
     */
    private $requiredProperties = array(        
        self::LINE_PROPERTY_NAME,
        self::CHARACTER_PROPERTY_NAME,
        self::REASON_PROPERTY_NAME
    );
    
    
    /**
     *
     * @var Entry
     */
    private $entry = null;
    
    
    /**
     * 
     * @param \stdClass $rawEntry
     * @return boolean
     */
    public function parse(\stdClass $rawEntryObject) {
        foreach ($this->requiredProperties as $requiredPropertyName) {
            if (!isset($rawEntryObject->$requiredPropertyName)) {
                throw new ParserException('Missing required property "'.$requiredPropertyName.'"', 1);
            }
        }
 
        $this->entry = new Entry();
        $this->entry->setLineNumber((int)$rawEntryObject->{self::LINE_PROPERTY_NAME});
        $this->entry->setColumnNumber((int)$rawEntryObject->{self::CHARACTER_PROPERTY_NAME});        
        
        if (isset($rawEntryObject->{self::ID_PROPERTY_NAME})) {
            $this->entry->setId($rawEntryObject->{self::ID_PROPERTY_NAME});
        }
        
        if (isset($rawEntryObject->{self::EVIDENCE_PROPERTY_NAME})) {
            $this->entry->setEvidence($rawEntryObject->{self::EVIDENCE_PROPERTY_NAME});
        }        
        
        if (isset($rawEntryObject->{self::RAw_PROPERTY_NAME})) {
            $this->entry->setRaw($rawEntryObject->{self::RAw_PROPERTY_NAME});
            
            if ($this->expectsParameters($rawEntryObject->{self::RAw_PROPERTY_NAME})) {
                $expectedParameterNames = $this->getExpectedParameterNames($rawEntryObject->{self::RAw_PROPERTY_NAME});
                $parameters = array();
                foreach ($expectedParameterNames as $expectedParameterName) {
                    if (!isset($rawEntryObject->$expectedParameterName)) {
                        throw new ParserException('Missing expected parameter "'.$expectedParameterName.'"', 2);
                    }                

                    $parameters[$expectedParameterName] = $rawEntryObject->$expectedParameterName;
                }

                $this->entry->setParameters($parameters);
            }            
        }
        
        $this->entry->setReason($rawEntryObject->{self::REASON_PROPERTY_NAME});        
        
        return true;
    } 
    
    
    private function expectsParameters($rawLine) {
        return $this->getExpectedParameterCount($rawLine) > 0;
    }
    
    
    private function getExpectedParameterCount($rawLine) {
        return count($this->getExpectedParameterNames($rawLine));
    }
    
    
    private function getExpectedParameterNames($rawLine) {        
        $matches = array();
        preg_match_all('/{[a-z]}/', $rawLine, $matches);
        
        $expectedParameterNames = array();
        
        foreach ($matches[0] as $parameterNameMatch) {
            $expectedParameterNames[] = substr($parameterNameMatch, 1, strlen($parameterNameMatch) - 2);
        }
        
        return $expectedParameterNames;       
    }
    
    
    /**
     * 
     * @return Entry
     */
    public function getEntry() {
        return $this->entry;
    }
    
}
<?php
namespace webignition\NodeJslintOutput\Entry;

use webignition\NodeJslintOutput\Entry\Entry;

class Serializer { 
    const ID_PROPERTY_NAME = 'id';
    const RAw_PROPERTY_NAME = 'raw';
    const EVIDENCE_PROPERTY_NAME = 'evidence';
    const LINE_PROPERTY_NAME = 'line';
    const CHARACTER_PROPERTY_NAME = 'character';
    const REASON_PROPERTY_NAME = 'reason';
    
    /**
     * Collection of standard property names
     * 
     * @var array
     */
    private $standardFields = array(
        self::ID_PROPERTY_NAME,
        self::RAw_PROPERTY_NAME,
        self::EVIDENCE_PROPERTY_NAME,
        self::LINE_PROPERTY_NAME,
        self::CHARACTER_PROPERTY_NAME,
        self::REASON_PROPERTY_NAME
    ); 
    
    /**
     *
     * @var \webignition\NodeJslintOutput\Entry\Entry
     */
    private $entry;
    
    
    /**
     *
     * @var boolean
     */
    private $excludeParameters = false;
    
    /**
     * 
     * @param \webignition\NodeJslintOutput\Entry\Entry $entry
     * @return string
     */        
    public function serialize(Entry $entry, $excludedFields = array()) {
        $this->entry = $entry;
        
        $fieldsToInclude = $this->getFieldsToInclude($excludedFields);

        $values = array();
        
        foreach ($fieldsToInclude as $fieldName) {
            $values[$fieldName] = $this->getEntryValue($fieldName);
        }
        
        return json_encode($values);
    }
    
    
    /**
     * 
     * @param \webignition\NodeJslintOutput\Entry\Entry $entry
     * @param array $excludedFields
     * @return array
     */
    private function getFieldsToInclude($excludedFields = array()) {
        $fieldsToInclude = array();
        
        foreach ($this->standardFields as $standardField) {
            if (!in_array($standardField, $excludedFields) && $standardField !== self::REASON_PROPERTY_NAME) {
                $fieldsToInclude[] = $standardField;
            }
        }
        
        if (!$this->getExcludeParameters()) {
            foreach ($this->entry->getParameters() as $parameterName => $parameterValue) {
                if (!in_array($parameterName, $excludedFields)) {
                    $fieldsToInclude[] = $parameterName;
                }
            }            
        }
        
        $fieldsToInclude[] = self::REASON_PROPERTY_NAME;        
        return $fieldsToInclude;
    }
    
    
    /**
     * 
     * @param string $fieldName
     * @return mixed
     */
    private function getEntryValue($fieldName) {
        if (in_array($fieldName, $this->standardFields)) {
            switch ($fieldName) {
                case self::ID_PROPERTY_NAME:
                    return $this->entry->getId();

                case self::RAw_PROPERTY_NAME:
                    return $this->entry->getRaw();

                case self::EVIDENCE_PROPERTY_NAME:
                    return $this->entry->getEvidence();

                case self::LINE_PROPERTY_NAME:
                    return $this->entry->getLineNumber();

                case self::CHARACTER_PROPERTY_NAME:
                    return $this->entry->getColumnNumber();

                case self::REASON_PROPERTY_NAME:
                    return $this->entry->getReason();
            }
        }
        
        $parameters = $this->entry->getParameters();
        return (isset($parameters[$fieldName])) ? $parameters[$fieldName] : null;
    }
    
    /**
     * 
     * @param boolean $excludeParameters
     */
    public function setExcludeParameters($excludeParameters) {
        $this->excludeParameters = $excludeParameters;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getExcludeParameters() {
        return $this->excludeParameters;
    }
    
}
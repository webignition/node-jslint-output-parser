<?php
namespace webignition\NodeJslintOutput\Entry;

/**
 * An entry in a nodejs-lint output
 */
class Entry { 
    
    
    /**
     *
     * @var string
     */
    private $id;
    
    /**
     *
     * @var string
     */
    private $raw;
    
    /**
     *
     * @var string
     */
    private $evidence;
    
    /**
     *
     * @var int
     */
    private $lineNumber;
    
    /**
     *
     * @var int
     */
    private $columnNumber;
    
    /**
     *
     * @var array
     */
    private $parameters = array();
    
    
    /**
     * 
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     * @param string $raw
     */
    public function setRaw($raw) {
        $this->raw = $raw;
    }
    
    /**
     * 
     * @return string
     */
    public function getRaw() {
        return $this->raw;
    }    
    
    /**
     * 
     * @param string $evidence
     */
    public function setEvidence($evidence) {
        $this->evidence = $evidence;
    }    
    
    /**
     * 
     * @return string
     */
    public function getEvidence() {
        return $this->evidence;
    }
    
    /**
     * 
     * @param int $lineNumber
     */
    public function setLineNumber($lineNumber) {
        $this->lineNumber = $lineNumber;
    }    
    
    /**
     * 
     * @return int
     */
    public function getLineNumber() {
        return $this->lineNumber;
    }    
    
    /**
     * 
     * @param int $lineNumber
     */    
    public function setColumnNumber($columnNumber) {
        $this->columnNumber = $columnNumber;
    }
    
    /**
     * 
     * @return int
     */    
    public function getColumnNumber() {
        return $this->columnNumber;
    }
    
    
    /**
     * 
     * @param array $parameters
     */
    public function setParameters($parameters = array()) {
        $this->parameters = $parameters;
    }
    
    
    /**
     * 
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function hasParameters() {
        return count($this->getParameters()) > 0;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getReason() {
        if (!$this->hasParameters()) {
            return $this->getRaw();
        }
        
        $parameterPlaceholders = array();
        $parameterValues = array();
        foreach ($this->getParameters() as $parameterName => $parameterValue) {
            $parameterPlaceholders[] = '{'.$parameterName.'}';
            $parameterValues[] = $parameterValue;
        }
        
        return str_replace($parameterPlaceholders, $parameterValues, $this->getRaw());
    }    
    
    
}
<?php

namespace webignition\NodeJslintOutput\Entry;

class Factory
{
    const KEY_ID = 'id';
    const KEY_RAW = 'raw';
    const KEY_EVIDENCE = 'evidence';
    const KEY_LINE = 'line';
    const KEY_CHARACTER = 'character';
    const KEY_REASON = 'reason';

    const UNEXPECTED_CONTROL_CHARACTER_ERROR = "Unexpected control character '{a}'.";

    /**
     * @var string[]
     */
    private $requiredProperties = [
        self::KEY_LINE,
        self::KEY_CHARACTER,
        self::KEY_REASON
    ];

    /**
     * @param array $entryData
     *
     * @return Entry
     *
     * @throws ParserException
     */
    public function create(array $entryData)
    {
        foreach ($this->requiredProperties as $requiredPropertyName) {
            if (!isset($entryData[$requiredPropertyName])) {
                throw new ParserException('Missing required property "'.$requiredPropertyName.'"', 1);
            }
        }

        $entry = new Entry();
        $entry->setLineNumber((int)$entryData[self::KEY_LINE]);
        $entry->setColumnNumber((int)$entryData[self::KEY_CHARACTER]);

        if (isset($entryData[self::KEY_ID])) {
            $entry->setId($entryData[self::KEY_ID]);
        }

        if (isset($entryData[self::KEY_EVIDENCE])) {
            $entry->setEvidence($entryData[self::KEY_EVIDENCE]);
        }

        if (isset($entryData[self::KEY_RAW])) {
            $entry->setRaw($entryData[self::KEY_RAW]);

            if ($this->expectsParameters($entryData[self::KEY_RAW])) {
                $entry->setParameters($this->getErrorParameters($entryData));
            }
        }

        $entry->setReason($entryData[self::KEY_REASON]);

        return $entry;
    }

    /**
     * @param array $entryData
     *
     * @return array
     *
     * @throws ParserException
     */
    private function getErrorParameters(array $entryData)
    {
        $expectedParameterNames = $this->getExpectedParameterNames($entryData[self::KEY_RAW]);
        $parameters = [];
        foreach ($expectedParameterNames as $expectedParameterName) {
            if (isset($entryData[$expectedParameterName])) {
                $parameters[$expectedParameterName] = $entryData[$expectedParameterName];
            } else {
                $parameter = $this->getDerivedParameter($entryData);

                if (is_null($parameter)) {
                    throw new ParserException('Missing expected parameter "'.$expectedParameterName.'"', 2);
                }

                $parameters[$expectedParameterName] = $parameter;
            }
        }

        return $parameters;
    }

    /**
     * @param array $entryData
     *
     * @return string|null
     */
    private function getDerivedParameter(array $entryData)
    {
        if (self::UNEXPECTED_CONTROL_CHARACTER_ERROR !== $entryData[self::KEY_RAW]) {
            return null;
        }

        return mb_substr($entryData[self::KEY_EVIDENCE], $entryData[self::KEY_CHARACTER], 1);
    }

    /**
     * @param string $rawLine
     *
     * @return bool
     */
    private function expectsParameters($rawLine)
    {
        return $this->getExpectedParameterCount($rawLine) > 0;
    }

    /**
     * @param string $rawLine
     *
     * @return int
     */
    private function getExpectedParameterCount($rawLine)
    {
        return count($this->getExpectedParameterNames($rawLine));
    }

    /**
     * @param string $rawLine
     *
     * @return array
     */
    private function getExpectedParameterNames($rawLine)
    {
        $matches = [];
        preg_match_all('/{[a-z]}/', $rawLine, $matches);

        $expectedParameterNames = [];

        foreach ($matches[0] as $parameterNameMatch) {
            $expectedParameterNames[] = substr($parameterNameMatch, 1, strlen($parameterNameMatch) - 2);
        }

        return $expectedParameterNames;
    }
}

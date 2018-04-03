<?php

namespace webignition\Tests\NodeJslintOutput;

use webignition\NodeJslintOutput\DecodedRawOutput;

class DecodedRawOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider isWellFormedDataProvider
     *
     * @param string $rawOutput
     * @param bool $expectedIsWellFormed
     */
    public function testIsWellFormed($rawOutput, $expectedIsWellFormed)
    {
        $decodedRawOutput = new DecodedRawOutput($rawOutput);

        $this->assertEquals($expectedIsWellFormed, $decodedRawOutput->isWellFormed());
    }

    /**
     * @return array
     */
    public function isWellFormedDataProvider()
    {
        return [
            'not an array' => [
                'rawOutput' => 'foo',
                'expectedIsWellFormed' => false,
            ],
            'item zero no a string' => [
                'rawOutput' => json_encode([
                    1,
                    1,
                ]),
                'expectedIsWellFormed' => false,
            ],
            'item one not a string' => [
                'rawOutput' => json_encode([
                    '/foo.js',
                    1,
                ]),
                'expectedIsWellFormed' => false,
            ],
            'well formed' => [
                'rawOutput' => json_encode([
                    '/foo.js',
                    [],
                ]),
                'expectedIsWellFormed' => true,
            ],
        ];
    }

    /**
     * @dataProvider getStatusLineDataProvider
     *
     * @param string $rawOutput
     * @param string|null $expectedStatusLine
     */
    public function testGetStatusLine($rawOutput, $expectedStatusLine)
    {
        $decodedRawOutput = new DecodedRawOutput($rawOutput);

        $this->assertEquals($expectedStatusLine, $decodedRawOutput->getStatusLine());
    }

    /**
     * @return array
     */
    public function getStatusLineDataProvider()
    {
        return [
            'not well formed' => [
                'rawOutput' => 'foo',
                'expectedStatusLine' => null,
            ],
            'well formed' => [
                'rawOutput' => json_encode([
                    '/foo.js',
                    [],
                ]),
                'expectedStatusLine' => '/foo.js',
            ],
        ];
    }

    /**
     * @dataProvider getLintResultDataProvider
     *
     * @param string $rawOutput
     * @param string|null $expectedLintResult
     */
    public function testGetLintResult($rawOutput, $expectedLintResult)
    {
        $decodedRawOutput = new DecodedRawOutput($rawOutput);

        $this->assertEquals($expectedLintResult, $decodedRawOutput->getLintResult());
    }

    /**
     * @return array
     */
    public function getLintResultDataProvider()
    {
        return [
            'not well formed' => [
                'rawOutput' => 'foo',
                'expectedLintResult' => null,
            ],
            'well formed' => [
                'rawOutput' => json_encode([
                    '/foo.js',
                    [],
                ]),
                'expectedLintResult' => [],
            ],
        ];
    }
}

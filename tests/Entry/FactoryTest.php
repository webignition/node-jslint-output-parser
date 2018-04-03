<?php

namespace webignition\Tests\NodeJslintOutput\Entry;

use webignition\NodeJslintOutput\Entry\Entry;
use webignition\NodeJslintOutput\Entry\Factory;
use webignition\NodeJslintOutput\Entry\ParserException;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new Factory();
    }

    /**
     * @dataProvider createMissingRequiredPropertyDataProvider
     *
     * @param array $entryData
     * @param string $expectedExceptionMessage
     *
     * @throws ParserException
     */
    public function testCreateMissingRequiredProperty(array $entryData, $expectedExceptionMessage)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->factory->create($entryData);
    }

    /**
     * @return array
     */
    public function createMissingRequiredPropertyDataProvider()
    {
        return [
            'empty' => [
                'entryData' => [],
                'expectedExceptionMessage' => 'Missing required property "line"',
            ],
            'missing character' => [
                'entryData' => [
                    Factory::KEY_LINE => 1,
                ],
                'expectedExceptionMessage' => 'Missing required property "character"',
            ],
            'missing reason' => [
                'entryData' => [
                    Factory::KEY_LINE => 1,
                    Factory::KEY_CHARACTER => 2,
                ],
                'expectedExceptionMessage' => 'Missing required property "reason"',
            ],
        ];
    }

    /**
     * @dataProvider createMissingExpectedParameterDataProvider
     *
     * @param array $entryData
     * @param string $expectedExceptionMessage
     *
     * @throws ParserException
     */
    public function testCreateMissingExpectedParameter(array $entryData, $expectedExceptionMessage)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionCode(2);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->factory->create($entryData);
    }

    /**
     * @return array
     */
    public function createMissingExpectedParameterDataProvider()
    {
        return [
            'missing a' => [
                'entryData' => [
                    'id' => '(error)',
                    'raw' => "Expected '{a}' and instead saw '{b}'.",
                    'code' => 'expected_a_b',
                    'evidence' => 'bar = 8',
                    'line' => 3,
                    'character' => 8,
                    'b' => 'foobar',
                    'reason' => "Expected ';' and instead saw 'foobar'.",
                ],
                'expectedExceptionMessage' => 'Missing expected parameter "a"',
            ],
            'missing b' => [
                'entryData' => [
                    'id' => '(error)',
                    'raw' => "Expected '{a}' and instead saw '{b}'.",
                    'code' => 'expected_a_b',
                    'evidence' => 'bar = 8',
                    'line' => 3,
                    'character' => 8,
                    'a' => ';',
                    'reason' => "Expected ';' and instead saw 'foobar'.",
                ],
                'expectedExceptionMessage' => 'Missing expected parameter "b"',
            ],
            'missing derived parameter' => [
                'entryData' => array_merge([
                    'id' => '(error)',
                    'raw' => "Foo '{a}'.",
                    'line' => 1,
                    'character' => 0,
                    'reason' => "Unexpected control character '{a}'.",
                ], json_decode('{"evidence": "\u0001"}', true)),
                'expectedExceptionMessage' => 'Missing expected parameter "a"',
            ],
        ];
    }

//    public function testFoo()
//    {
////        var_dump(json_decode('{
////      "id": "(error)",
////      "raw": "Unexpected control character \'{a}\'.",
////      "evidence": "eval((function(s){var a,c,e,i,j,o=\"\",r,t=\"\u0001",
////      "line": 1,
////      "character": 43,
////      "reason": "Unexpected control character \'{a}\'."
////    }', true));
//
//        var_dump(array_merge([
//            'id' => 'foo',
//        ], json_decode('{
//      "evidence": "eval((function(s){var a,c,e,i,j,o=\"\",r,t=\"\u0001"
//    }', true)));
//        exit();
//    }

    /**
     * @dataProvider createSuccessDataProvider
     *
     * @param array $entryData
     *
     * @param string $expectedId
     * @param string $expectedRaw
     * @param string $expectedEvidence
     * @param int $expectedLineNumber
     * @param int $expectedColumnNumber
     * @param array $expectedParameters
     * @param string $expectedReason
     *
     * @throws ParserException
     */
    public function testCreateSuccess(
        array $entryData,
        $expectedId,
        $expectedRaw,
        $expectedEvidence,
        $expectedLineNumber,
        $expectedColumnNumber,
        $expectedParameters,
        $expectedReason
    ) {
        $entry = $this->factory->create($entryData);

        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals($expectedId, $entry->getId());
        $this->assertEquals($expectedRaw, $entry->getRaw());
        $this->assertEquals($expectedEvidence, $entry->getEvidence());
        $this->assertEquals($expectedLineNumber, $entry->getLineNumber());
        $this->assertEquals($expectedColumnNumber, $entry->getColumnNumber());
        $this->assertEquals($expectedParameters, $entry->getParameters());
        $this->assertEquals($expectedReason, $entry->getReason());
    }

    /**
     * @return array
     */
    public function createSuccessDataProvider()
    {
        return [
            'default' => [
                'entryData' => [
                    'id' => '(error)',
                    'raw' => "Expected '{a}' and instead saw '{b}'.",
                    'code' => 'expected_a_b',
                    'evidence' => 'bar = 8',
                    'line' => 3,
                    'character' => 8,
                    'a' => ';',
                    'b' => 'foobar',
                    'reason' => "Expected ';' and instead saw 'foobar'.",
                ],
                'expectedId' => '(error)',
                'expectedRaw' => "Expected '{a}' and instead saw '{b}'.",
                'expectedEvidence' => 'bar = 8',
                'expectedLineNumber' => 3,
                'expectedColumnNumber' => 8,
                'expectedParameters' => [
                    'a' => ';',
                    'b' => 'foobar',
                ],
                'expectedReason' => "Expected ';' and instead saw 'foobar'.",
            ],
            'valid derived parameter' => [
                'entryData' => array_merge([
                    'id' => '(error)',
                    'raw' => "Unexpected control character '{a}'.",
                    'line' => 1,
                    'character' => 0,
                    'reason' => "Unexpected control character '{a}'.",
                ], json_decode('{"evidence": "\u0001"}', true)),
                'expectedId' => '(error)',
                'expectedRaw' => "Unexpected control character '{a}'.",
                'expectedEvidence' => json_decode('"\u0001"'),
                'expectedLineNumber' => 1,
                'expectedColumnNumber' => 0,
                'expectedParameters' => [
                    'a' => json_decode('"\u0001"'),
                ],
                'expectedReason' => "Unexpected control character '{a}'.",
            ],
        ];
    }
}
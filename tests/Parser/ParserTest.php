<?php

namespace webignition\Tests\NodeJslintOutput\Parser;

use webignition\NodeJslintOutput\Entry\ParserException;
use webignition\NodeJslintOutput\Exception;
use webignition\NodeJslintOutput\NodeJslintOutput;
use webignition\NodeJslintOutput\Parser as Parser;
use webignition\Tests\NodeJslintOutput\Factory\FixtureLoader;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parser = new Parser();
    }

    /**
     * @dataProvider parseExceptionOutputDataProvider
     *
     * @param string $rawOutput
     * @param string $expectedExceptionMessage
     *
     * @throws Exception
     * @throws ParserException
     */
    public function testParseExceptionOutput($rawOutput, $expectedExceptionMessage, $expectedExceptionCode)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->expectExceptionCode($expectedExceptionCode);

        $this->parser->parse($rawOutput);
    }

    /**
     * @return array
     */
    public function parseExceptionOutputDataProvider()
    {
        return [
            'input file not found' => [
                'rawOutput' => FixtureLoader::load('InputFileNotFound.txt'),
                'expectedExceptionMessage' => 'Input file "/home/example/script.js" not found',
                'expectedExceptionCode' => Exception::CODE_INPUT_FILE_NOT_FOUND,
            ],
            'unexpected output' => [
                'rawOutput' => FixtureLoader::load('UnexpectedOutput.txt'),
                'expectedExceptionMessage' => 'Unexpected output; is not a lint result set',
                'expectedExceptionCode' => Exception::CODE_UNEXPECTED_OUTPUT,
            ],
            'incorrect nodejs path' => [
                'rawOutput' => FixtureLoader::load('IncorrectNodeJsPathOutput.txt'),
                'expectedExceptionMessage' =>
                    'node-jslint not found at "/home/example/node_modules/jslint/bin/jslint.js"',
                'expectedExceptionCode' => Exception::CODE_INCORRECT_NODE_JS_PATH,
            ],
        ];
    }

    /**
     * @dataProvider parseSuccessDataProvider
     *
     * @param string $rawOutput
     * @param bool $expectedEntryCount
     * @param int $expectedPercentScanned
     * @param string $expectedStatusLine
     * @param bool $expectedHasTooManyErrors
     * @param bool $expectedWasStopped
     *
     * @throws Exception
     * @throws ParserException
     */
    public function testParseSuccess(
        $rawOutput,
        $expectedEntryCount,
        $expectedPercentScanned,
        $expectedStatusLine,
        $expectedHasTooManyErrors,
        $expectedWasStopped
    ) {
        $output = $this->parser->parse($rawOutput);

        $this->assertInstanceOf(NodeJslintOutput::class, $output);
        $this->assertEquals($expectedEntryCount, $output->getEntryCount());
        $this->assertEquals($expectedPercentScanned, $output->getPercentScanned());
        $this->assertEquals($expectedStatusLine, $output->getStatusLine());
        $this->assertEquals($expectedHasTooManyErrors, $output->hasTooManyErrors());
        $this->assertEquals($expectedWasStopped, $output->wasStopped());
    }

    /**
     * @return array
     */
    public function parseSuccessDataProvider()
    {
        return [
            'error-free output' => [
                'rawOutput' => FixtureLoader::load('ErrorFreeOutput.json'),
                'expectedEntryCount' => 0,
                'expectedPercentScanned' => 100,
                'expectedStatusLine' => '/home/example/source.js',
                'expectedHasTooManyErrors' => false,
                'expectedWasStopped' => false,
            ],
            'partial scan; too many errors, 7% scanned' => [
                'rawOutput' => FixtureLoader::load('PartialScanTooManyErrorsSevenPercentOutput.json'),
                'expectedEntryCount' => 51,
                'expectedPercentScanned' => 7,
                'expectedStatusLine' => '/home/example/source.js',
                'expectedHasTooManyErrors' => true,
                'expectedWasStopped' => false,
            ],
            'partial scan; stopping, 50% scanned' => [
                'rawOutput' => FixtureLoader::load('PartialScanStoppingFiftyPercentOutput.json'),
                'expectedEntryCount' => 16,
                'expectedPercentScanned' => 50,
                'expectedStatusLine' => '/home/example/source.js',
                'expectedHasTooManyErrors' => false,
                'expectedWasStopped' => true,
            ],
            'large output' => [
                'rawOutput' => FixtureLoader::load('LargeOutput.json'),
                'expectedEntryCount' => 65,
                'expectedPercentScanned' => 87,
                'expectedStatusLine' => '/home/example/source.js',
                'expectedHasTooManyErrors' => false,
                'expectedWasStopped' => true,
            ],
            'invalid control character lacking parameter' => [
                'rawOutput' => FixtureLoader::load('InvalidControlCharacterLackingParameter.json'),
                'expectedEntryCount' => 51,
                'expectedPercentScanned' => 16,
                'expectedStatusLine' => '/tmp/b4360cc769b30969524df17380ab36c1:1:1378822370.3284',
                'expectedHasTooManyErrors' => true,
                'expectedWasStopped' => false,
            ],
            'stopping; 100% scanned' => [
                'rawOutput' => FixtureLoader::load('StoppingOneHundredPercentOutput.json'),
                'expectedEntryCount' => 2,
                'expectedPercentScanned' => 100,
                'expectedStatusLine' => '/tmp/2be27b536970c6988a1f387359237529:1:1379085668.7688',
                'expectedHasTooManyErrors' => false,
                'expectedWasStopped' => true,
            ],
            'stopping; 75% scanned' => [
                'rawOutput' => FixtureLoader::load('StoppingEntryWithSingleSpaceOutput.json'),
                'expectedEntryCount' => 2,
                'expectedPercentScanned' => 75,
                'expectedStatusLine' => '/tmp/41ce43a0e2a1c6530cda88fe7ee06d48:192:1381751332.6812',
                'expectedHasTooManyErrors' => false,
                'expectedWasStopped' => true,
            ],
        ];
    }

    /**
     * @dataProvider parseNonWellFormedOutputDataProvider
     *
     * @param string $rawOutput
     * @throws Exception
     * @throws ParserException
     */
    public function testParseNonWellFormedOutput($rawOutput)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::CODE_UNEXPECTED_OUTPUT);
        $this->expectExceptionMessage('Unexpected output; is not a lint result set');

        $this->parser->parse($rawOutput);
    }

    /**
     * @return array
     */
    public function parseNonWellFormedOutputDataProvider()
    {
        return [
            'not an array' => [
                'rawOutput' => 'foo',
            ],
            'item zero no a string' => [
                'rawOutput' => json_encode([
                    1,
                    1,
                ]),
            ],
            'item one not a string' => [
                'rawOutput' => json_encode([
                    '/foo.js',
                    1,
                ]),
            ],
        ];
    }

    public function testParseNotAString()
    {
        $this->assertFalse($this->parser->parse(1));
    }
}

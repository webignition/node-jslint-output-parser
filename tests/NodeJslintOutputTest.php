<?php

namespace webignition\Tests\NodeJslintOutput;

use webignition\NodeJslintOutput\Entry\Entry;
use webignition\NodeJslintOutput\NodeJslintOutput;

class NodeJslintOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEntries()
    {
        /* @var Entry $entry0 */
        $entry0 = \Mockery::mock(Entry::class);

        /* @var Entry $entry1 */
        $entry1 = \Mockery::mock(Entry::class);

        $output = new NodeJslintOutput();

        $output->addEntry($entry0);
        $output->addEntry($entry1);

        $this->assertEquals([$entry0, $entry1], $output->getEntries());
    }
}

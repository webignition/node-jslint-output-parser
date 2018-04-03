<?php

namespace webignition\NodeJslintOutput;

class RawOutputDecoder
{
    /**
     * @param string $rawOutput
     *
     * @return mixed
     */
    public static function decode($rawOutput)
    {
        return json_decode($rawOutput, true);
    }
}

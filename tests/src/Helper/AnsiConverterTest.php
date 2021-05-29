<?php

namespace Tests\PHPCensor\Helper;

use PHPUnit\Framework\TestCase;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class AnsiConverterTest extends TestCase
{
    public function testConvert_convertToHtml()
    {
        $input          = "\e[31mThis is red !\e[0m";
        $expectedOutput = '<span class="ansi_color_bg_black ansi_color_fg_red">This is red !</span>';

        $converter    = new AnsiToHtmlConverter(null, false);
        $actualOutput = $converter->convert($input);

        self::assertEquals($expectedOutput, $actualOutput);
    }
}

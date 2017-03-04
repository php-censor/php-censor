<?php

namespace PHPCensor\Helper;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

/**
 * Converts ANSI output to HTML.
 */
final class AnsiConverter
{
    static private $converter = null;

    /**
     * Initialize the singleton.
     *
     * @return AnsiToHtmlConverter
     */
    private static function getInstance()
    {
        if (self::$converter === null) {
            self::$converter = new AnsiToHtmlConverter(null, false);
        }

        return self::$converter;
    }

    /**
     * Convert a text containing ANSI color sequences into HTML code.
     *
     * @param string $text The text to convert
     *
     * @return string The HTML code.
     */
    public static function convert($text)
    {
        return self::getInstance()->convert($text);
    }

    /**
     * Do not instantiate this class.
     */
    private function __construct()
    {
    }
}

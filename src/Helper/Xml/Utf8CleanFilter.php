<?php

namespace PHPCensor\Helper\Xml;

use php_user_filter;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Utf8CleanFilter extends php_user_filter
{
    const PATTERN = '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u';

    /**
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = \stream_bucket_make_writeable($in)) {
            $bucket->data = \preg_replace(self::PATTERN, '', $bucket->data);
            $consumed     += $bucket->datalen;

            \stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}

<?php

namespace PHPCensor\Helper;

use voku\helper\AntiXSS;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Template
{
    /**
     * @var AntiXSS
     */
    protected static $antiXss = null;

    /**
     * @param string $string
     *
     * @return string
     */
    public static function clean($string)
    {
        if (self::$antiXss === null) {
            self::$antiXss = new AntiXSS();
        }

        $antiXss = self::$antiXss;

        return $antiXss->xss_clean($string);
    }
}

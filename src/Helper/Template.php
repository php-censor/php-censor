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
    protected static AntiXSS $antiXss;

    /**
     * @param mixed $param
     * @return mixed
     */
    public static function clean($param)
    {
        if ($param === null) {
            return null;
        }

        if (is_bool($param) || is_int($param) || is_float($param)) {
            return $param;
        }

        if (self::$antiXss === null) {
            self::$antiXss = new AntiXSS();
        }

        $antiXss = self::$antiXss;

        return $antiXss->xss_clean((string)$param);
    }
}

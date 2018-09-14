<?php

namespace PHPCensor\Helper;

use voku\helper\AntiXSS;

class Template
{
    /**
     * @var AntiXSS
     */
    static protected $antiXss = null;

    /**
     * @param string $string
     *
     * @return string
     */
    static public function clean($string)
    {
        if (self::$antiXss === null) {
            self::$antiXss = new AntiXSS();
        }

        $antiXss = self::$antiXss;

        return $antiXss->xss_clean($string);
    }
}

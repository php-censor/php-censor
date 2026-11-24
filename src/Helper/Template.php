<?php

namespace PHPCensor\Helper;

use voku\helper\AntiXSS;

class Template
{
    protected static ?AntiXSS $antiXss = null;

    public static function clean(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        if (self::$antiXss === null) {
            self::$antiXss = new AntiXSS();
        }

        $antiXss = self::$antiXss;

        return $antiXss->xss_clean($string);
    }
}

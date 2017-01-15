<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace Tests\PHPCensor\Helper;

use DateTime;
use PHPCensor\Helper\Lang;
use Tests\PHPCensor\LocalizationTestCase;

class LangTest extends LocalizationTestCase
{
    /**
     * @return array
     */
    public function localizationsProvider()
    {
        $directory = SRC_DIR . 'Languages' . DIRECTORY_SEPARATOR;
        $languages = [];
        foreach(glob($directory . '*') as $file) {
            $language    = include($file);
            $languages[$file] = [
                $language
            ];
        }

        return $languages;
    }

    /**
     * @dataProvider localizationsProvider
     */
    /*public function testLocalizations(array $strings)
    {
        $directory = SRC_DIR . 'Languages' . DIRECTORY_SEPARATOR;
        $en        = include($directory . 'lang.en.php');

        foreach ($en as $enIndex => $enString) {
            $this->assertArrayHasKey($enIndex, $strings);
        }
    }*/
}

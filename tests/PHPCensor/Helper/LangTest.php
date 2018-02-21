<?php

namespace Tests\PHPCensor\Helper;

use Tests\PHPCensor\LocalizationTestCase;

class LangTest extends LocalizationTestCase
{
    public function testSuccess()
    {
        self::assertTrue(true);
    }

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
            self::assertArrayHasKey($enIndex, $strings);
        }
    }*/
}

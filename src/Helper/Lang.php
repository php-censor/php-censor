<?php

namespace PHPCensor\Helper;

use PHPCensor\Config;
use PHPCensor\Store\Factory;
use PHPCensor\Store\UserStore;

/**
 * Languages Helper Class - Handles loading strings files and the strings within them.
 */
class Lang
{
    const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string
     */
    protected static $language  = null;

    /**
     * @var array
     */
    protected static $languages = [];

    /**
     * @var array
     */
    protected static $strings = [];

    /**
     * @var array
     */
    protected static $defaultStrings = [];

    /**
     * Get a specific string from the language file.
     *
     * @param array  ...$params
     *
     * @return string
     */
    public static function get(...$params)
    {
        $string = $params[0];
        if (array_key_exists($string, self::$strings)) {
            $params[0] = self::$strings[$string];
            return call_user_func_array('sprintf', $params);
        } elseif (self::DEFAULT_LANGUAGE !== self::$language && array_key_exists($string, self::$defaultStrings)) {
            $params[0] = self::$defaultStrings[$string];
            return call_user_func_array('sprintf', $params);
        }

        return $string;
    }

    /**
     * Get the currently active language.
     *
     * @return string|null
     */
    public static function getLanguage()
    {
        return self::$language;
    }

    /**
     * Try and load a language, and if successful, set it for use throughout the system.
     *
     * @param $language
     *
     * @return bool
     */
    public static function setLanguage($language)
    {
        if (in_array($language, self::$languages)) {
            self::$language = $language;
            self::$strings  = self::loadLanguage();
            return true;
        }

        return false;
    }

    /**
     * Return a list of available languages and their names.
     *
     * @return array
     */
    public static function getLanguageOptions()
    {
        $languages = [];
        foreach (self::$languages as $language) {
            $strings = include(SRC_DIR . 'Languages/lang.' . $language . '.php');
            $languages[$language] = !empty($strings['language_name'])
                ? $strings['language_name'] . ' (' . $language . ')'
                : $language;
        }

        return $languages;
    }

    /**
     * Get the strings for the currently active language.
     *
     * @return string[]
     */
    public static function getStrings()
    {
        return self::$strings;
    }

    /**
     * Initialise the Language helper, try load the language file for the user's browser or the configured default.
     *
     * @param Config $config
     * @param string $languageForce
     */
    public static function init(Config $config, $languageForce = null)
    {
        self::$defaultStrings = self::loadLanguage(self::DEFAULT_LANGUAGE);
        self::loadAvailableLanguages();

        if ($languageForce && self::setLanguage($languageForce)) {
            return;
        }

        $user = null;
        if (!empty($_SESSION['php-censor-user-id'])) {
            /** @var UserStore $userStore */
            $userStore = Factory::getStore('User');
            $user      = $userStore->getById($_SESSION['php-censor-user-id']);
        }

        if ($user) {
            $language = $user->getLanguage();
            if ($user && self::setLanguage($language)) {
                return;
            }
        }

        // Try the installation default language:
        $language = $config->get('php-censor.language', self::DEFAULT_LANGUAGE);
        if (self::setLanguage($language)) {
            return;
        }
    }

    /**
     * Load a specific language file.
     *
     * @param string $language
     *
     * @return string[]|null
     */
    protected static function loadLanguage($language = null)
    {
        $language = $language
            ? $language
            : self::$language;

        $langFile = SRC_DIR . 'Languages/lang.' . $language . '.php';

        if (!file_exists($langFile)) {
            return null;
        }

        $strings = include($langFile);
        if (is_null($strings) || !is_array($strings) || !count($strings)) {
            return null;
        }

        return $strings;
    }

    /**
     * Load the names of all available languages.
     */
    protected static function loadAvailableLanguages()
    {
        $matches = [];
        foreach (glob(SRC_DIR . 'Languages/lang.*.php') as $file) {
            if (preg_match('/lang\.([a-z]{2}\-?[a-z]*)\.php/', $file, $matches)) {
                self::$languages[] = $matches[1];
            }
        }
    }
}

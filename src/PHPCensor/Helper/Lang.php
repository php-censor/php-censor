<?php

namespace PHPCensor\Helper;

use b8\Config;
use b8\Store\Factory;
use PHPCensor\Model\User;
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
    protected static $default_strings = [];

    /**
     * Get a specific string from the language file.
     *
     * @param $string
     * @return mixed|string
     */
    public static function get($string)
    {
        $vars = func_get_args();

        if (array_key_exists($string, self::$strings)) {
            $vars[0] = self::$strings[$string];
            return call_user_func_array('sprintf', $vars);
        } elseif (self::DEFAULT_LANGUAGE !== self::$language && array_key_exists($string, self::$default_strings)) {
            $vars[0] = self::$default_strings[$string];
            return call_user_func_array('sprintf', $vars);
        }

        return $string;
    }

    /**
     * Output a specific string from the language file.
     */
    public static function out()
    {
        print call_user_func_array(['PHPCensor\Helper\Lang', 'get'], func_get_args());
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
            $strings = include(SRC_DIR . 'Languages' . DIRECTORY_SEPARATOR . 'lang.' . $language . '.php');
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
     * @param string $language_force
     */
    public static function init(Config $config, $language_force = null)
    {
        self::$default_strings = self::loadLanguage(self::DEFAULT_LANGUAGE);
        self::loadAvailableLanguages();

        if ($language_force && self::setLanguage($language_force)) {
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
        $language = $language ? $language : self::$language;
        $langFile = SRC_DIR . 'Languages' . DIRECTORY_SEPARATOR . 'lang.' . $language . '.php';

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
        foreach (glob(SRC_DIR . 'Languages' . DIRECTORY_SEPARATOR . 'lang.*.php') as $file) {
            if (preg_match('/lang\.([a-z]{2}\-?[a-z]*)\.php/', $file, $matches)) {
                self::$languages[] = $matches[1];
            }
        }
    }
}

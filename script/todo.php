<?php
/**
 * Todo generator for languages.
 *
 * @internal Inspired from Laravel-lang project.
 */
class TodoGenerator
{
    /**
     * Base path.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Excluded directories.
     *
     * @var array
     */
    protected $excluded = ['lang.en.php'];

    /**
     * Path of output file.
     *
     * @var string
     */
    protected $output;

    /**
     * Construct.
     *
     * @param string $basePath base path
     * @param array  $excluded excluded directories
     */
    public function __construct($basePath, $excluded = [])
    {
        $this->basePath = realpath($basePath);

        if (!empty($excluded)) {
            $this->excluded = $excluded;
        }

        $this->load();
    }

    /**
     * Returns object.
     *
     * @param string $basePath base path
     * @param array  $excluded excluded directories
     *
     * @return TodoGenerator
     */
    public static function make($basePath, $excluded = [])
    {
        return new self($basePath, $excluded);
    }

    /**
     * Save todo list.
     *
     * @param string $path path
     */
    public function save($path)
    {
        file_put_contents($path, $this->output);
    }

    /**
     * Compare translations and generate file.
     */
    private function load()
    {
        // Get English version
        $english = $this->getTranslations(__DIR__.'/../src/Languages/lang.en.php');

        $languages = $this->getLanguages();

        $this->output = "# Todo list for languages\n\nTo generate this file, launch this command:\n\n";
        $this->output .= "    php script/todo.php\n\n";
        $this->compareTranslations($english, $languages);
    }

    /**
     * Returns array of translations by language.
     *
     * @param string $language language code
     *
     * @return array
     */
    private function getTranslations($language)
    {
        return [
            include($language)
        ];
    }

    /**
     * Returns list of languages.
     *
     * @return array
     */
    private function getLanguages()
    {
        $directories = glob($this->basePath.'/*.php');

        $languages = array_map(function ($dir) {
            $name = basename($dir);

            return in_array($name, $this->excluded, true) ? null : $name;
        }, $directories);

        return array_filter($languages);
    }

    /**
     * Compare translations.
     *
     * @param array $default   language by default
     * @param array $languages others languages
     */
    private function compareTranslations(array $default, array $languages)
    {
        // Return diff language by language
        foreach ($languages as $language) {
            $this->output .= "\n * ".$language.":\n";
            $current = $this->getTranslations("{$this->basePath}/{$language}");

            foreach ($default as $key => $values) {
                foreach ($values as $key2 => $value2) {
                    if (!isset($current[$key][$key2])) {
                        $this->output .= '    * '.$key2."\n";
                    }
                }
            }
        }
    }
}

TodoGenerator::make(__DIR__.'/../src/Languages')->save(__DIR__.'/../todo_languages.md');

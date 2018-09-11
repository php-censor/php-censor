<?php

namespace PHPCensor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Check localizations.
 */
class CheckLocalizationCommand extends Command
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var array
     */
    protected $excluded = ['lang.en.php'];

    /**
     * Construct.
     */
    public function __construct()
    {
        parent::__construct();

        $this->basePath = __DIR__.'/../Languages';
    }

    /**
     * Configure.
     */
    protected function configure()
    {
        $this
            ->setName('php-censor:check-localizations')
            ->addOption(
                'same',
                0,
                InputOption::VALUE_OPTIONAL,
                'Same than English version (0 = no, 1 = yes)'
            )
            ->addOption(
                'langs',
                [],
                InputOption::VALUE_OPTIONAL,
                'List of languages separated by commas. By default, all languages'
            )
            ->setDescription('Check localizations.');
    }

    /**
     * Loops through running.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\n<info>Check localizations!</info>");

        $sameThanEnglish = (null !== $input->getOption('same'))
            ? $input->getOption('same')
            : false;

        $languagesList = (null !== $input->getOption('langs'))
            ? explode(',', $input->getOption('langs'))
            : [];

        // Get English version
        $english         = $this->getTranslations($this->basePath.'/lang.en.php');
        $othersLanguages = $this->getLanguages($languagesList);
        $diffs           = $this->compareTranslations($english, $othersLanguages);

        foreach ($diffs as $language => $value) {
            $output->writeln(sprintf("%s:", $language));
            if (!empty($value['not_present'])) {
                $output->writeln("\tNot present:\n\t\t" . implode("\n\t\t", $value['not_present']));
            }

            if ($sameThanEnglish === '1' && !empty($value['same'])) {
                $output->writeln("\tSame than English:\n\t\t" . implode("\n\t\t", $value['same']));
            }
        }
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
     * @param array $languagesList
     *
     * @return array
     */
    private function getLanguages(array $languagesList = [])
    {
        $files = glob($this->basePath . '/*.php');

        $languages = array_map(function ($dir) use ($languagesList) {
            $name = basename($dir);

            if (in_array($name, $this->excluded, true)) {
                return null;
            }

            // Check if in list of languages.
            if (!empty($languagesList)) {
                $languageOfName = explode('.', $name);

                if (null == $languageOfName[1] || !in_array($languageOfName[1], $languagesList)) {
                    return null;
                }
            }

            return $name;
        }, $files);

        return array_filter($languages);
    }

    /**
     * Compare translations.
     *
     * @param array $default   language by default
     * @param array $languages others languages
     *
     * @return array
     */
    private function compareTranslations(array $default, array $languages)
    {
        $diffs = [];

        // Return diff language by language
        foreach ($languages as $language) {
            $current = $this->getTranslations($this->basePath.'/'.$language);

            foreach ($default as $key => $values) {
                $keyValues = array_keys($values);

                foreach ($keyValues as $key2) {
                    if (!isset($current[$key][$key2])) {
                        $diffs[$language]['not_present'][] = $key2;
                    } elseif ($current[$key][$key2] === $default[$key][$key2]) {
                        $diffs[$language]['same'][] = $key2;
                    }
                }
            }
        }

        return $diffs;
    }
}

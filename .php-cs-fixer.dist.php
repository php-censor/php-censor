<?php

use PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->exclude(['vendor'])
    ->in(__DIR__)
;

$config = new Config();

return $config
    ->setRules([
        '@PSR1'                       => true,
        '@PSR2'                       => true,
        'array_syntax'                => ['syntax' => 'short'],
        'blank_line_before_statement' => ['statements' => ['return', 'throw']],
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed'         => true,
        ],
        'no_unneeded_curly_braces' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
        ],
    ])
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder)
    ;

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
        '@PSR1'                        => true,
        '@PSR12'                       => true,
        'strict_param'                 => true,
        'strict_comparison'            => true,
        //'declare_strict_types'         => true,
        'fully_qualified_strict_types' => true,
        'native_function_invocation'   => ['include' => ['@internal'], 'scope' => 'all', 'strict' => true],
        'array_syntax'                 => ['syntax' => 'short'],
        'blank_line_before_statement'  => ['statements' => ['return', 'throw', 'break', 'continue']],
        'general_phpdoc_tag_rename'    => ['replacements' => [
            'inheritDocs' => 'inheritDoc',
            'inheritdocs' => 'inheritDoc',
            'inheritdoc'  => 'inheritDoc',
        ]],
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed'         => true,
            'allow_unused_params' => true,
        ],
        'no_unneeded_curly_braces' => [
            'namespaces' => true,
        ],
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
        ],
    ])
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder)
    ;

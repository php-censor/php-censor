<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        InlineConstructorDefaultToPropertyRector::class,
        ExplicitNullableParamTypeRector::class,
    ])
    ->withPhpSets(php81: true)
    ->withPreparedSets(
        deadCode: true,
        //codeQuality: true,
        //typeDeclarations: true,
    );

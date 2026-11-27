<?php

declare(strict_types=1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildMetaInterface
{
    public const KEY_DATA             = 'data';
    public const KEY_META             = 'meta';
    public const KEY_ERRORS           = 'errors';
    public const KEY_WARNINGS         = 'warnings';
    public const KEY_COVERAGE         = 'coverage';
    public const KEY_COVERAGE_LINES   = 'coverage_lines';
    public const KEY_COVERAGE_METHODS = 'coverage_methods';
    public const KEY_COVERAGE_CLASSES = 'coverage_classes';
    public const KEY_SUMMARY          = 'summary';

    public function getKey(): ?string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getPlugin(): ?string;
}

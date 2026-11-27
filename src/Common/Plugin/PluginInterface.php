<?php

declare(strict_types=1);

namespace PHPCensor\Common\Plugin;

use PHPCensor\Common\Build\BuildInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface PluginInterface
{
    public static function getName(): string;

    /**
     * @throws \Throwable
     */
    public function execute(): bool;

    public static function canExecute(string $stage, BuildInterface $build): bool;
}

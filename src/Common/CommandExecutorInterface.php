<?php

declare(strict_types=1);

namespace PHPCensor\Common;

use PHPCensor\Common\Exception\Exception;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface CommandExecutorInterface
{
    /**
     * @param mixed ...$params
     */
    public function executeCommand(...$params): bool;

    public function enableCommandOutput(): void;

    public function disableCommandOutput(): void;

    public function isEnabledCommandOutput(): bool;

    public function getLastCommandOutput(): string;

    /**
     * @throws Exception
     */
    public function findBinary(
        array $binaryNames,
        string $binaryPath = ''
    ): string;
}

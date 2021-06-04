<?php

declare(strict_types = 1);

namespace PHPCensor\Logging;

use Monolog\Formatter\LineFormatter;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class AnsiFormatter extends LineFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        return \str_replace(
            ["\033[0;31m", "\033[0m", "\033[0;32m", "\033[0;36m"],
            '',
            parent::format($record)
        );
    }
}

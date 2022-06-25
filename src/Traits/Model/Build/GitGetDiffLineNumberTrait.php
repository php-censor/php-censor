<?php

declare(strict_types=1);

namespace PHPCensor\Traits\Model\Build;

use PHPCensor\Builder;
use PHPCensor\Model\Build\GitBuild;

/**
 * Provides some basic diff processing functionality.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 *
 * @mixin GitBuild
 */
trait GitGetDiffLineNumberTrait
{
    /**
     * Uses git diff to figure out what the diff line position is, based on the error line number.
     */
    protected function getDiffLineNumber(Builder $builder, string $file, int $line): ?int
    {
        $builder->logExecOutput(false);

        $prNumber = $this->getExtra('pull_request_number');
        $path = $builder->buildPath;

        if (!empty($prNumber)) {
            $builder->executeCommand('cd "%s" && git diff "origin/%s" "%s"', $path, $this->getBranch(), $file);
        } else {
            $commitId = $this->getCommitId();
            $compare = empty($commitId) ? 'HEAD' : $commitId;

            $builder->executeCommand('cd "%s" && git diff "%s^^" "%s"', $path, $compare, $file);
        }

        $builder->logExecOutput(true);

        $diff = $builder->getLastCommandOutput();

        $lines = $this->getLinePositions($diff);

        return $lines[$line] ?? null;
    }

    /**
     * Take a diff
     */
    private function getLinePositions(string $diff): ?array
    {
        if (empty($diff)) {
            return null;
        }

        $rtn = [];

        $diffLines = \explode(PHP_EOL, $diff);

        while (\count($diffLines)) {
            $line = \array_shift($diffLines);

            if (\substr($line, 0, 2) === '@@') {
                \array_unshift($diffLines, $line);

                break;
            }
        }

        $lineNumber = 0;
        $position = 0;

        foreach ($diffLines as $diffLine) {
            if (\preg_match('/@@\s+\-[0-9]+\,[0-9]+\s+\+([0-9]+)\,([0-9]+)/', $diffLine, $matches)) {
                $lineNumber = (int)$matches[1] - 1;
            }

            $rtn[$lineNumber] = $position;

            $lineNumber++;
            $position++;
        }

        return $rtn;
    }
}

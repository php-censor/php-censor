<?php

declare(strict_types=1);

namespace PHPCensor\Common\Project;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ProjectInterface
{
    public function getId(): ?int;

    public function getTitle(): ?string;

    /**
     * Returns build configuration for the project.
     */
    public function getBuildConfig(): array;

    public function getLink(): string;
}

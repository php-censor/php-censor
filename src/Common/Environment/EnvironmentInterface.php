<?php

declare(strict_types=1);

namespace PHPCensor\Common\Environment;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface EnvironmentInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getProjectId(): ?int;

    public function getBranches(): array;
}

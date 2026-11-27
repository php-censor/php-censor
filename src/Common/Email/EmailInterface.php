<?php

declare(strict_types=1);

namespace PHPCensor\Common\Email;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface EmailInterface
{
    public const DEFAULT_FROM = 'PHP Censor <no-reply@php-censor.local>';

    public function addEmailTo(string $email, ?string $name = null): self;

    public function setSubject(string $subject): self;

    public function setBody(string $body): self;

    public function setIsHtml(bool $isHtml = false): self;

    public function addCarbonCopyEmail(string $email, ?string $name = null): self;

    public function getEmailsTo(): array;

    public function getSubject(): string;

    public function getBody(): string;

    public function isHtml(): bool;

    public function getCarbonCopyEmails(): array;
}

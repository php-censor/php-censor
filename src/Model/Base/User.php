<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Model;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class User extends Model
{
    protected array $data = [
        'id'            => null,
        'email'         => null,
        'hash'          => null,
        'is_admin'      => 0,
        'name'          => null,
        'language'      => null,
        'per_page'      => null,
        'provider_key'  => 'internal',
        'provider_data' => null,
        'remember_key'  => null,
    ];

    protected array $dataTypes = [
        'is_admin'      => 'boolean',
        'per_page'      => 'integer',
        'provider_data' => 'array'
    ];

    public function getEmail(): ?string
    {
        return $this->getDataItem('email');
    }

    public function setEmail(string $value): bool
    {
        return $this->setDataItem('email', $value);
    }

    public function getHash(): ?string
    {
        return $this->getDataItem('hash');
    }

    public function setHash(string $value): bool
    {
        return $this->setDataItem('hash', $value);
    }

    public function getIsAdmin(): bool
    {
        return $this->getDataItem('is_admin');
    }

    public function setIsAdmin(bool $value): bool
    {
        return $this->setDataItem('is_admin', $value);
    }

    public function getName(): ?string
    {
        return $this->getDataItem('name');
    }

    public function setName(string $value): bool
    {
        return $this->setDataItem('name', $value);
    }

    public function getLanguage(): ?string
    {
        return $this->getDataItem('language');
    }

    public function setLanguage(?string $value): bool
    {
        return $this->setDataItem('language', $value);
    }

    public function getPerPage(): ?int
    {
        return $this->getDataItem('per_page');
    }

    public function setPerPage(?int $value): bool
    {
        return $this->setDataItem('per_page', $value);
    }

    public function getProviderKey(): ?string
    {
        return $this->getDataItem('provider_key');
    }

    public function setProviderKey(string $value): bool
    {
        return $this->setDataItem('provider_key', $value);
    }

    /**
     * @return mixed
     */
    public function getProviderData(string $key = null)
    {
        $data = $this->getDataItem('provider_data');
        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? null;
    }

    public function setProviderData(array $value): bool
    {
        return $this->setDataItem('provider_data', $value);
    }

    public function getRememberKey(): ?string
    {
        return $this->getDataItem('remember_key');
    }

    public function setRememberKey(?string $value): bool
    {
        return $this->setDataItem('remember_key', $value);
    }
}

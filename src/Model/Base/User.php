<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model;

class User extends Model
{
    /**
     * @var array
     */
    protected $data = [
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

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->data['id'];
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function setId(int $value)
    {
        if ($this->data['id'] === $value) {
            return false;
        }

        $this->data['id'] = $value;

        return $this->setModified('id');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->data['email'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setEmail(string $value)
    {
        if ($this->data['email'] === $value) {
            return false;
        }

        $this->data['email'] = $value;

        return $this->setModified('email');
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->data['hash'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setHash(string $value)
    {
        if ($this->data['hash'] === $value) {
            return false;
        }

        $this->data['hash'] = $value;

        return $this->setModified('hash');
    }

    /**
     * @return bool
     */
    public function getIsAdmin()
    {
        return (bool)$this->data['is_admin'];
    }

    /**
     * @param bool $value
     *
     * @return bool
     */
    public function setIsAdmin(bool $value)
    {
        if ($this->data['is_admin'] === (int)$value) {
            return false;
        }

        $this->data['is_admin'] = (int)$value;

        return $this->setModified('is_admin');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setName(string $value)
    {
        if ($this->data['name'] === $value) {
            return false;
        }

        $this->data['name'] = $value;

        return $this->setModified('name');
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->data['language'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setLanguage($value)
    {
        if ($this->data['language'] === $value) {
            return false;
        }

        $this->data['language'] = $value;

        return $this->setModified('language');
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return (int)$this->data['per_page'];
    }

    /**
     * @param int|null $value
     *
     * @return bool
     */
    public function setPerPage(?int $value)
    {
        if ($this->data['per_page'] === $value) {
            return false;
        }

        $this->data['per_page'] = $value;

        return $this->setModified('per_page');
    }

    /**
     * @return string
     */
    public function getProviderKey()
    {
        return $this->data['provider_key'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setProviderKey(string $value)
    {
        if ($this->data['provider_key'] === $value) {
            return false;
        }

        $this->data['provider_key'] = $value;

        return $this->setModified('provider_key');
    }

    /**
     * @param string|null $key
     *
     * @return array|string|null
     */
    public function getProviderData($key = null)
    {
        $data         = json_decode($this->data['provider_data'], true);
        $providerData = null;
        if (is_null($key)) {
            $providerData = $data;
        } elseif (isset($data[$key])) {
            $providerData = $data[$key];
        }

        return $providerData;
    }

    /**
     * @param array $value
     *
     * @return bool
     */
    public function setProviderData(array $value)
    {
        $providerData = json_encode($value);
        if ($this->data['provider_data'] === $providerData) {
            return false;
        }

        $this->data['provider_data'] = $providerData;

        return $this->setModified('provider_data');
    }

    /**
     * @return string
     */
    public function getRememberKey()
    {
        return $this->data['remember_key'];
    }

    /**
     * @param string|null $value
     *
     * @return bool
     */
    public function setRememberKey(?string $value)
    {
        if ($this->data['remember_key'] === $value) {
            return false;
        }

        $this->data['remember_key'] = $value;

        return $this->setModified('remember_key');
    }
}

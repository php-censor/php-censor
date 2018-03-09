<?php

namespace PHPCensor\Model\Base;

use PHPCensor\Model;

class User extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'user';

    /**
     * @var array
     */
    protected $data = [
        'id'            => null,
        'email'         => null,
        'hash'          => null,
        'is_admin'      => null,
        'name'          => null,
        'language'      => null,
        'per_page'      => null,
        'provider_key'  => null,
        'provider_data' => null,
        'remember_key'  => null,
    ];

    /**
     * @return integer
     */
    public function getId()
    {
        return (integer)$this->data['id'];
    }

    /**
     * @param integer $value
     */
    public function setId($value)
    {
        $this->validateNotNull('id', $value);
        $this->validateInt('id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;

        $this->setModified('id');
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
     */
    public function setEmail($value)
    {
        $this->validateNotNull('email', $value);
        $this->validateString('email', $value);

        if ($this->data['email'] === $value) {
            return;
        }

        $this->data['email'] = $value;

        $this->setModified('email');
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
     */
    public function setHash($value)
    {
        $this->validateNotNull('hash', $value);
        $this->validateString('hash', $value);

        if ($this->data['hash'] === $value) {
            return;
        }

        $this->data['hash'] = $value;

        $this->setModified('hash');
    }

    /**
     * @return boolean
     */
    public function getIsAdmin()
    {
        return (boolean)$this->data['is_admin'];
    }

    /**
     * @param boolean $value
     */
    public function setIsAdmin($value)
    {
        $this->validateNotNull('is_admin', $value);
        $this->validateBoolean('is_admin', $value);

        if ($this->data['is_admin'] === $value) {
            return;
        }

        $this->data['is_admin'] = (integer)$value;

        $this->setModified('is_admin');
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
     */
    public function setName($value)
    {
        $this->validateNotNull('name', $value);
        $this->validateString('name', $value);

        if ($this->data['name'] === $value) {
            return;
        }

        $this->data['name'] = $value;

        $this->setModified('name');
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
     */
    public function setLanguage($value)
    {
        if ($this->data['language'] === $value) {
            return;
        }

        $this->data['language'] = $value;

        $this->setModified('language');
    }

    /**
     * @return integer
     */
    public function getPerPage()
    {
        return (integer)$this->data['per_page'];
    }

    /**
     * @param integer $value
     */
    public function setPerPage($value)
    {
        $this->validateInt('per_page', $value);

        if ($this->data['per_page'] === $value) {
            return;
        }

        $this->data['per_page'] = $value;

        $this->setModified('per_page');
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
     */
    public function setProviderKey($value)
    {
        $this->validateNotNull('provider_key', $value);
        $this->validateString('provider_key', $value);

        if ($this->data['provider_key'] === $value) {
            return;
        }

        $this->data['provider_key'] = $value;

        $this->setModified('provider_key');
    }

    /**
     * @param string|null $key
     *
     * @return array|string|null
     */
    public function getProviderData($key = null)
    {
        $data  = json_decode($this->data['provider_data'], true);
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
     */
    public function setProviderData(array $value)
    {
        $this->validateNotNull('provider_data', $value);

        $providerData = json_encode($value);
        if ($this->data['provider_data'] === $providerData) {
            return;
        }

        $this->data['provider_data'] = $providerData;

        $this->setModified('provider_data');
    }

    /**
     * @return string
     */
    public function getRememberKey()
    {
        return $this->data['remember_key'];
    }

    /**
     * @param string $value
     */
    public function setRememberKey($value)
    {
        $this->validateString('remember_key', $value);

        if ($this->data['remember_key'] === $value) {
            return;
        }

        $this->data['remember_key'] = $value;

        $this->setModified('remember_key');
    }
}

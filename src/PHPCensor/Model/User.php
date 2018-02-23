<?php

namespace PHPCensor\Model;

use b8\Config;
use PHPCensor\Model;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
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
     * @var array
     */
    protected $getters = [
        'id'            => 'getId',
        'email'         => 'getEmail',
        'hash'          => 'getHash',
        'is_admin'      => 'getIsAdmin',
        'name'          => 'getName',
        'language'      => 'getLanguage',
        'per_page'      => 'getPerPage',
        'provider_key'  => 'getProviderKey',
        'provider_data' => 'getProviderData',
        'remember_key'  => 'getRememberKey',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'id'            => 'setId',
        'email'         => 'setEmail',
        'hash'          => 'setHash',
        'is_admin'      => 'setIsAdmin',
        'name'          => 'setName',
        'language'      => 'setLanguage',
        'per_page'      => 'setPerPage',
        'provider_key'  => 'setProviderKey',
        'provider_data' => 'setProviderData',
        'remember_key'  => 'setRememberKey',
    ];

    /**
     * @return int
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        $rtn = $this->data['email'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        $rtn = $this->data['hash'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $rtn = $this->data['name'];

        return $rtn;
    }

    /**
     * @return int
     */
    public function getIsAdmin()
    {
        $rtn = $this->data['is_admin'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getProviderKey()
    {
        $rtn = $this->data['provider_key'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getProviderData()
    {
        $rtn = $this->data['provider_data'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getRememberKey()
    {
        $rtn = $this->data['remember_key'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        $rtn = $this->data['language'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getPerPage()
    {
        $rtn = $this->data['per_page'];

        return $rtn;
    }

    /**
     * @param $value int
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
     * @param $value string
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
     * @param $value string
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
     * @param $value string
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
     * @param $value int
     */
    public function setIsAdmin($value)
    {
        $this->validateNotNull('is_admin', $value);
        $this->validateInt('is_admin', $value);

        if ($this->data['is_admin'] === $value) {
            return;
        }

        $this->data['is_admin'] = $value;

        $this->setModified('is_admin');
    }

    /**
     * @param $value string
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
     * @param $value string
     */
    public function setProviderData($value)
    {
        $this->validateString('provider_data', $value);

        if ($this->data['provider_data'] === $value) {
            return;
        }

        $this->data['provider_data'] = $value;

        $this->setModified('provider_data');
    }

    /**
     * @param $value string
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

    /**
     * @param $value string
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
     * @param $value string
     */
    public function setPerPage($value)
    {
        if ($this->data['per_page'] === $value) {
            return;
        }

        $this->data['per_page'] = $value;

        $this->setModified('per_page');
    }

    /**
     * @return integer
     */
    public function getFinalPerPage()
    {
        $perPage = $this->getPerPage();
        if ($perPage) {
            return (integer)$perPage;
        }

        return (integer)Config::getInstance()->get('php-censor.per_page', 10);
    }
}

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
     * @var array
     */
    public static $sleepable = [];

    /**
     * @var string
     */
    protected $tableName = 'user';

    /**
     * @var string
     */
    protected $modelName = 'User';

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
    ];

    /**
     * @var array
     */
    protected $getters = [
        // Direct property getters:
        'id'            => 'getId',
        'email'         => 'getEmail',
        'hash'          => 'getHash',
        'is_admin'      => 'getIsAdmin',
        'name'          => 'getName',
        'language'      => 'getLanguage',
        'per_page'      => 'getPerPage',
        'provider_key'  => 'getProviderKey',
        'provider_data' => 'getProviderData',
        // Foreign key getters:
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'            => 'setId',
        'email'         => 'setEmail',
        'hash'          => 'setHash',
        'is_admin'      => 'setIsAdmin',
        'name'          => 'setName',
        'language'      => 'setLanguage',
        'per_page'      => 'setPerPage',
        'provider_key'  => 'setProviderKey',
        'provider_data' => 'setProviderData',
        // Foreign key setters:
    ];

    /**
     * @var array
     */
    public $columns = [
        'id' => [
            'type'           => 'int',
            'length'         => 11,
            'primary_key'    => true,
            'auto_increment' => true,
            'default'        => null,
        ],
        'email' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => null,
        ],
        'hash' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => null,
        ],
        'is_admin' => [
            'type'   => 'int',
            'length' => 11,
        ],
        'name' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => null,
        ],
        'language' => [
            'type'    => 'varchar',
            'length'  => 5,
            'default' => null,
        ],
        'per_page' => [
            'type'    => 'int',
            'length'  => 11,
            'default' => null,
        ],
        'provider_key' => [
            'type'    => 'varchar',
            'length'  => 255,
            'default' => 'internal',
        ],
        'provider_data' => [
            'type'     => 'varchar',
            'length'   => 255,
            'nullable' => true,
            'default'  => null,
        ],
    ];

    /**
     * @var array
     */
    public $indexes = [
        'PRIMARY'   => ['unique' => true, 'columns' => 'id'],
        'idx_email' => ['unique' => true, 'columns' => 'email'],
        'email'     => ['unique' => true, 'columns' => 'email'],
        'name'      => ['columns' => 'name'],
    ];

    /**
     * @var array
     */
    public $foreignKeys = [];

    /**
     * Get the value of Id / id.
     *
     * @return int
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
     * Get the value of Email / email.
     *
     * @return string
     */
    public function getEmail()
    {
        $rtn = $this->data['email'];

        return $rtn;
    }

    /**
     * Get the value of Hash / hash.
     *
     * @return string
     */
    public function getHash()
    {
        $rtn = $this->data['hash'];

        return $rtn;
    }

    /**
     * Get the value of Name / name.
     *
     * @return string
     */
    public function getName()
    {
        $rtn = $this->data['name'];

        return $rtn;
    }

    /**
     * Get the value of IsAdmin / is_admin.
     *
     * @return int
     */
    public function getIsAdmin()
    {
        $rtn = $this->data['is_admin'];

        return $rtn;
    }

    /**
     * Get the value of ProviderKey / provider_key.
     *
     * @return string
     */
    public function getProviderKey()
    {
        $rtn = $this->data['provider_key'];

        return $rtn;
    }

    /**
     * Get the value of ProviderData / provider_data.
     *
     * @return string
     */
    public function getProviderData()
    {
        $rtn = $this->data['provider_data'];

        return $rtn;
    }

    /**
     * Get the value of Language / language.
     *
     * @return string
     */
    public function getLanguage()
    {
        $rtn = $this->data['language'];

        return $rtn;
    }

    /**
     * Get the value of PerPage / per_page.
     *
     * @return string
     */
    public function getPerPage()
    {
        $rtn = $this->data['per_page'];

        return $rtn;
    }

    /**
     * Set the value of Id / id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setId($value)
    {
        $this->validateNotNull('Id', $value);
        $this->validateInt('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;

        $this->setModified('id');
    }

    /**
     * Set the value of Email / email.
     *
     * Must not be null.
     * @param $value string
     */
    public function setEmail($value)
    {
        $this->validateNotNull('Email', $value);
        $this->validateString('Email', $value);

        if ($this->data['email'] === $value) {
            return;
        }

        $this->data['email'] = $value;

        $this->setModified('email');
    }

    /**
     * Set the value of Hash / hash.
     *
     * Must not be null.
     * @param $value string
     */
    public function setHash($value)
    {
        $this->validateNotNull('Hash', $value);
        $this->validateString('Hash', $value);

        if ($this->data['hash'] === $value) {
            return;
        }

        $this->data['hash'] = $value;

        $this->setModified('hash');
    }

    /**
     * Set the value of Name / name.
     *
     * Must not be null.
     * @param $value string
     */
    public function setName($value)
    {
        $this->validateNotNull('Name', $value);
        $this->validateString('Name', $value);

        if ($this->data['name'] === $value) {
            return;
        }

        $this->data['name'] = $value;

        $this->setModified('name');
    }

    /**
     * Set the value of IsAdmin / is_admin.
     *
     * Must not be null.
     * @param $value int
     */
    public function setIsAdmin($value)
    {
        $this->validateNotNull('IsAdmin', $value);
        $this->validateInt('IsAdmin', $value);

        if ($this->data['is_admin'] === $value) {
            return;
        }

        $this->data['is_admin'] = $value;

        $this->setModified('is_admin');
    }

    /**
     * Set the value of ProviderKey / provider_key.
     *
     * Must not be null.
     * @param $value string
     */
    public function setProviderKey($value)
    {
        $this->validateNotNull('ProviderKey', $value);
        $this->validateString('ProviderKey', $value);

        if ($this->data['provider_key'] === $value) {
            return;
        }

        $this->data['provider_key'] = $value;

        $this->setModified('provider_key');
    }

    /**
     * Set the value of ProviderData / provider_data.
     *
     * @param $value string
     */
    public function setProviderData($value)
    {
        $this->validateString('ProviderData', $value);

        if ($this->data['provider_data'] === $value) {
            return;
        }

        $this->data['provider_data'] = $value;

        $this->setModified('provider_data');
    }

    /**
     * Set the value of Language / language.
     *
     * Must not be null.
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
     * Set the value of PerPage / per_page.
     *
     * Must not be null.
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

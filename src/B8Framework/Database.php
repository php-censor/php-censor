<?php

namespace b8;

class Database extends \PDO
{
    const MYSQL_TYPE      = 'mysql';
    const POSTGRESQL_TYPE = 'pgsql';

    /**
     * @var string
     */
    protected $type = 'read';

    /**
     * @var boolean
     */
    protected static $initialised = false;

    /**
     * @var array
     */
    protected static $servers = [
        'read'  => [],
        'write' => []
    ];

    /**
     * @var array
     */
    protected static $connections = [
        'read'  => null,
        'write' => null
    ];

    /**
     * @var array
     */
    protected static $dsn = [
        'read'  => '',
        'write' => ''
    ];

    protected static $details = [];

    /**
     * @param string $table
     *
     * @return string
     */
    public function lastInsertIdExtended($table = null)
    {
        if ($table && self::POSTGRESQL_TYPE === $this->getAttribute(self::ATTR_DRIVER_NAME)) {
            return parent::lastInsertId('"' . $table . '_id_seq"');
        }

        return parent::lastInsertId();
    }

    protected static function init()
    {
        $config   = Config::getInstance();
        $settings = $config->get('b8.database', []);

        self::$servers['read']  = $settings['servers']['read'];
        self::$servers['write'] = $settings['servers']['write'];

        self::$details['driver'] = $settings['type'];
        self::$details['db']     = $settings['name'];
        self::$details['user']   = $settings['username'];
        self::$details['pass']   = $settings['password'];

        self::$initialised = true;
    }

    /**
     * @param string $type
     *
     * @return \b8\Database
     *
     * @throws \Exception
     */
    public static function getConnection($type = 'read')
    {
        if (!self::$initialised) {
            self::init();
        }

        if (is_null(self::$connections[$type])) {
            // Shuffle, so we pick a random server:
            $servers = self::$servers[$type];
            shuffle($servers);

            $connection = null;

            // Loop until we get a working connection:
            while (count($servers)) {
                // Pull the next server:
                $server = array_shift($servers);

                self::$dsn[$type] = self::$details['driver'] . ':host=' . $server['host'];
                if (isset($server['port'])) {
                    self::$dsn[$type] .= ';port=' . (integer)$server['port'];
                }
                self::$dsn[$type] .= ';dbname=' . self::$details['db'];

                $pdoOptions = [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT    => 2,
                ];
                if (self::MYSQL_TYPE === self::$details['driver']) {
                    $pdoOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'UTF8'";
                }

                // Try to connect:
                try {
                    $connection = new self(
                        self::$dsn[$type],
                        self::$details['user'],
                        self::$details['pass'],
                        $pdoOptions
                    );
                    $connection->setType($type);
                } catch (\PDOException $ex) {
                    $connection = false;
                }

                // Opened a connection? Break the loop:
                if ($connection) {
                    break;
                }
            }

            // No connection? Oh dear.
            if (!$connection && $type === 'read') {
                throw new \Exception('Could not connect to any ' . $type . ' servers.');
            }

            self::$connections[$type] = $connection;
        }

        return self::$connections[$type];
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return self::$details;
    }

    /**
     * @return string
     */
    public function getDsn()
    {
        return self::$dsn[$this->type];
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public static function reset()
    {
        self::$connections = ['read' => null, 'write' => null];
        self::$initialised = false;
    }

    /**
     * @param string $statement
     *
     * @return string
     */
    protected function quoteNames($statement)
    {
        $quote = '';
        if (self::MYSQL_TYPE === self::$details['driver']) {
            $quote = '`';
        } elseif (self::POSTGRESQL_TYPE === self::$details['driver']) {
            $quote = '"';
        }

        return preg_replace('/{{(.*?)}}/', ($quote . '\1' . $quote), $statement);
    }

    /**
     * @param string $statement
     * @param array  $driver_options
     *
     * @return \PDOStatement
     */
    public function prepareCommon($statement, array $driver_options = [])
    {
        return parent::prepare($this->quoteNames($statement), $driver_options);
    }
}

<?php

namespace b8;

class Database extends \PDO
{
    protected static $initialised = false;
    protected static $servers     = ['read' => [], 'write' => []];
    protected static $connections = ['read' => null, 'write' => null];
    protected static $details     = [];
    protected static $lastUsed    = ['read' => null, 'write' => null];

    /**
     * @param string $table
     *
     * @return string
     */
    public function lastInsertIdExtended($table = null)
    {
        if ($table && $this->getAttribute(self::ATTR_DRIVER_NAME) == 'pgsql') {
            return parent::lastInsertId($table . '_id_seq');
        }

        return parent::lastInsertId();
    }

    protected static function init()
    {
        $config   = Config::getInstance();
        $settings = $config->get('b8.database', []);

        self::$servers['read']  = $settings['servers']['read'];
        self::$servers['write'] = $settings['servers']['write'];
        self::$details['type']  = $settings['type'];
        self::$details['db']    = $settings['name'];
        self::$details['user']  = $settings['username'];
        self::$details['pass']  = $settings['password'];

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

        // If the connection hasn't been used for 5 minutes, force a reconnection:
        if (!is_null(self::$lastUsed[$type]) && (time() - self::$lastUsed[$type]) > 300) {
            self::$connections[$type] = null;
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

                $dns = self::$details['type'] . ':host=' . $server['host'];
                if (isset($server['port'])) {
                    $dns .= ';port=' . (integer)$server['port'];
                }
                $dns .= ';dbname=' . self::$details['db'];

                $pdoOptions = [
                    \PDO::ATTR_PERSISTENT         => false,
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT            => 2,
                ];
                if ('mysql' === self::$details['type']) {
                    $pdoOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'UTF8'";
                }

                // Try to connect:
                try {
                    $connection = new self(
                        $dns,
                        self::$details['user'],
                        self::$details['pass'],
                        $pdoOptions
                    );
                } catch (\PDOException $ex) {
                    $connection = false;
                }

                // Opened a connection? Break the loop:
                if ($connection) {
                    break;
                }
            }

            // No connection? Oh dear.
            if (!$connection && $type == 'read') {
                throw new \Exception('Could not connect to any ' . $type . ' servers.');
            }

            self::$connections[$type] = $connection;
        }

        self::$lastUsed[$type] = time();

        return self::$connections[$type];
    }

    public function getDetails()
    {
        return self::$details;
    }

    public static function reset()
    {
        self::$connections = ['read' => null, 'write' => null];
        self::$lastUsed    = ['read' => null, 'write' => null];
        self::$initialised = false;
    }

    public function prepareCommon($statement, array $driver_options = [])
    {
        $quote = '';
        if ('mysql' === self::$details['type']) {
            $quote = '`';
        } elseif ('pgsql' === self::$details['type']) {
            $quote = '"';
        }

        $statement = preg_replace('/{{(.*?)}}/', ($quote . '\1' . $quote), $statement);

        return parent::prepare($statement, $driver_options);
    }
}

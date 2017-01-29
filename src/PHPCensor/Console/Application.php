<?php

namespace PHPCensor\Console;

use b8\Config;
use Symfony\Component\Console\Application as BaseApplication;
use Phinx\Console\Command\Create;
use Phinx\Console\Command\Migrate;
use Phinx\Console\Command\Rollback;
use Phinx\Console\Command\Status;
use Phinx\Config\Config as PhinxConfig;

class Application extends BaseApplication
{
    /**
     * Constructor.
     *
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = 'PHP Censor - Continuous Integration for PHP', $version = '')
    {
        parent::__construct($name, $version);

        $applicationConfig = Config::getInstance();
        $databaseSettings  = $applicationConfig->get('b8.database', []);

        $phinxSettings = [
            'paths' => [
                'migrations' => 'src/PHPCensor/Migrations',
            ],
            'environments'                => [
                'default_migration_table' => 'migration',
                'default_database'        => 'php-censor',
                'php-censor'              => [
                    'adapter' => $databaseSettings['type'],
                    'host'    => $databaseSettings['servers']['write'][0]['host'],
                    'port'    => $databaseSettings['servers']['write'][0]['port'],
                    'name'    => $databaseSettings['name'],
                    'user'    => $databaseSettings['username'],
                    'pass'    => $databaseSettings['password'],
                ],
            ],
        ];

        $phinxConfig = new PhinxConfig($phinxSettings);

        $this->add(
            (new Create())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:create')
        );
        $this->add(
            (new Migrate())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:migrate')
        );
        $this->add(
            (new Rollback())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:rollback')
        );
        $this->add(
            (new Status())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:status')
        );
    }
}

<?php

namespace PHPCensor\Console;

use b8\Config;
use b8\Store\Factory;
use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\Command\InstallCommand;
use PHPCensor\Command\PollCommand;
use PHPCensor\Command\RebuildCommand;
use PHPCensor\Command\RebuildQueueCommand;
use PHPCensor\Command\RunCommand;
use PHPCensor\Command\WorkerCommand;
use PHPCensor\Logging\LoggerConfig;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\UserStore;
use Symfony\Component\Console\Application as BaseApplication;
use Phinx\Console\Command\Create;
use Phinx\Console\Command\Migrate;
use Phinx\Console\Command\Rollback;
use Phinx\Console\Command\Status;
use Phinx\Config\Config as PhinxConfig;

/**
 * Class Application
 * 
 * @package PHPCensor\Console
 */
class Application extends BaseApplication
{
    /**
     * Constructor.
     *
     * @param string       $name         The name of the application
     * @param string       $version      The version of the application
     * @param LoggerConfig $loggerConfig Logger config
     */
    public function __construct($name = 'PHP Censor - Continuous Integration for PHP', $version = '', LoggerConfig $loggerConfig = null)
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
                    'name'    => $databaseSettings['name'],
                    'user'    => $databaseSettings['username'],
                    'pass'    => $databaseSettings['password'],
                ],
            ],
        ];
        
        if (!empty($databaseSettings['port'])) {
            $phinxSettings['environments']['php-censor']['port'] = (integer)$databaseSettings['port'];
        }

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
        
        /** @var UserStore $userStore */
        $userStore = Factory::getStore('User');
        
        /** @var ProjectStore $projectStore */
        $projectStore = Factory::getStore('Project');
        
        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');

        $this->add(new RunCommand($loggerConfig->getFor('RunCommand')));
        $this->add(new RebuildCommand($loggerConfig->getFor('RunCommand')));
        $this->add(new InstallCommand());
        $this->add(new PollCommand($loggerConfig->getFor('PollCommand')));
        $this->add(new CreateAdminCommand($userStore));
        $this->add(new CreateBuildCommand($projectStore, new BuildService($buildStore)));
        $this->add(new WorkerCommand($loggerConfig->getFor('WorkerCommand')));
        $this->add(new RebuildQueueCommand($loggerConfig->getFor('RebuildQueueCommand')));
    }
}

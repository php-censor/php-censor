<?php

namespace PHPCensor\Console;

use Exception;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Phinx\Config\Config as PhinxConfig;
use Phinx\Console\Command\Create;
use Phinx\Console\Command\Migrate;
use Phinx\Console\Command\Rollback;
use Phinx\Console\Command\Status;
use PHPCensor\Command\CheckLocalizationCommand;
use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\Command\InstallCommand;
use PHPCensor\Command\RemoveOldBuildsCommand;
use PHPCensor\Command\RebuildQueueCommand;
use PHPCensor\Command\WorkerCommand;
use PHPCensor\Config;
use PHPCensor\Logging\AnsiFormatter;
use PHPCensor\Logging\Handler;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\Factory;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\UserStore;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Class Application
 *
 * @package PHPCensor\Console
 */
class Application extends BaseApplication
{
    const LOGO = <<<'LOGO'
    ____  __  ______    ______
   / __ \/ / / / __ \  / ____/__  ____  _________  _____
  / /_/ / /_/ / /_/ / / /   / _ \/ __ \/ ___/ __ \/ ___/
 / ____/ __  / ____/ / /___/  __/ / / (__  ) /_/ / /
/_/   /_/ /_/_/      \____/\___/_/ /_/____/\____/_/


LOGO;

    /**
     * @param Config $applicationConfig
     *
     * @return Logger
     * @throws Exception
     */
    protected function initLogger(Config $applicationConfig)
    {
        $rotate   = (bool)$applicationConfig->get('php-censor.log.rotate', false);
        $maxFiles = (int)$applicationConfig->get('php-censor.log.max_files', 0);

        /** @var HandlerInterface[] $loggerHandlers */
        $loggerHandlers = [];
        if ($rotate) {
            $loggerHandlers[] = new RotatingFileHandler(RUNTIME_DIR . 'console.log', $maxFiles, Logger::DEBUG);
        } else {
            $loggerHandlers[] = new StreamHandler(RUNTIME_DIR . 'console.log', Logger::DEBUG);
        }

        $loggerHandlers[0]->setFormatter(new AnsiFormatter());

        $logger = new Logger('php-censor', $loggerHandlers);
        Handler::register($logger);

        return $logger;
    }

    /**
     * @param string $name
     * @param string $version
     *
     * @throws Exception
     */
    public function __construct($name = 'PHP Censor', $version = 'UNKNOWN')
    {
        $version = trim(file_get_contents(ROOT_DIR . 'VERSION.md'));

        parent::__construct($name, $version);

        $applicationConfig = Config::getInstance();
        $databaseSettings  = $applicationConfig->get('php-censor.database', []);
        if (!$databaseSettings) {
            $databaseSettings  = $applicationConfig->get('b8.database', []);
        }

        $phinxSettings = [];
        if ($databaseSettings) {
            $phinxSettings = [
                'paths' => [
                    'migrations' => ROOT_DIR . 'src/Migrations',
                ],
                'environments' => [
                    'default_migration_table' => 'migration',
                    'default_database' => 'php-censor',
                    'php-censor' => [
                        'adapter' => $databaseSettings['type'],
                        'host' => $databaseSettings['servers']['write'][0]['host'],
                        'name' => $databaseSettings['name'],
                        'user' => $databaseSettings['username'],
                        'pass' => $databaseSettings['password'],
                    ],
                ],
            ];
        }

        if (!empty($databaseSettings['port'])) {
            $phinxSettings['environments']['php-censor']['port'] =
                (int)$databaseSettings['port'];
        }

        if (!empty($databaseSettings['servers']['write'][0]['port'])) {
            $phinxSettings['environments']['php-censor']['port'] =
                (int)$databaseSettings['servers']['write'][0]['port'];
        }

        if (!empty($databaseSettings['type'])
            && $databaseSettings['type'] === 'pgsql'
        ) {
            if (!array_key_exists('pgsql-sslmode', $databaseSettings['servers']['write'][0])) {
                $databaseSettings['servers']['write'][0]['pgsql-sslmode'] = 'prefer';
            }

            $phinxSettings['environments']['php-censor']['host'] .=
                ';sslmode=' . $databaseSettings['servers']['write'][0]['pgsql-sslmode'];
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
        $buildStore   = Factory::getStore('Build');

        $buildService = new BuildService($buildStore, $projectStore);
        $logger       = $this->initLogger($applicationConfig);

        $this->add(new InstallCommand());
        $this->add(new CreateAdminCommand($userStore));
        $this->add(new CreateBuildCommand($projectStore, $buildService));
        $this->add(new RemoveOldBuildsCommand($projectStore, $buildService));
        $this->add(new WorkerCommand($logger, $buildService));
        $this->add(new RebuildQueueCommand($logger));
        $this->add(new CheckLocalizationCommand());
    }

    /**
     * Returns help.
     *
     * @return string
     */
    public function getHelp()
    {
        return self::LOGO . parent::getHelp();
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion()
    {
        return sprintf('<info>%s</info> v%s', $this->getName(), $this->getVersion());
    }
}

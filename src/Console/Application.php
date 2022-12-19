<?php

declare(strict_types=1);

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
use PHPCensor\BuildFactory;
use PHPCensor\Command\CheckLocalizationCommand;
use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\Command\InstallCommand;
use PHPCensor\Command\RemoveOldBuildsCommand;
use PHPCensor\Command\RebuildQueueCommand;
use PHPCensor\Command\WorkerCommand;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Logging\AnsiFormatter;
use PHPCensor\Logging\Handler;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildMetaStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\Store\UserStore;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Application extends BaseApplication
{
    private const LOGO = <<<'LOGO'
    ____  __  ______    ______
   / __ \/ / / / __ \  / ____/__  ____  _________  _____
  / /_/ / /_/ / /_/ / / /   / _ \/ __ \/ ___/ __ \/ ___/
 / ____/ __  / ____/ / /___/  __/ / / (__  ) /_/ / /
/_/   /_/ /_/_/      \____/\___/_/ /_/____/\____/_/


LOGO;

    private ConfigurationInterface $configuration;

    private DatabaseManager $databaseManager;

    /**
     * @throws Exception
     */
    protected function initLogger(ConfigurationInterface $applicationConfig): Logger
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

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        UserStore $userStore,
        ProjectStore $projectStore,
        ProjectGroupStore $projectGroupStore,
        BuildStore $buildStore,
        BuildMetaStore $buildMetaStore,
        BuildErrorStore $buildErrorStore,
        SecretStore $secretStore,
        EnvironmentStore $environmentStore,
        string $name = 'PHP Censor',
        string $version = 'UNKNOWN'
    ) {
        $version = \trim(\file_get_contents(ROOT_DIR . 'VERSION.md'));
        $version = !empty($version) ? $version : '0.0.0 (UNKNOWN)';

        parent::__construct($name, $version);

        $this->configuration   = $configuration;
        $this->databaseManager = $databaseManager;

        $oldDatabaseSettings = $this->configuration->get('b8.database', []);
        $databaseSettings    = $this->configuration->get('php-censor.database', []);
        if ($oldDatabaseSettings && !$databaseSettings) {
            throw new InvalidArgumentException(
                'Missing database settings in application config "config.yml" (Section: "php-censor.database")'
            );
        }

        $phinxSettings = [];
        if ($databaseSettings) {
            $phinxSettings = [
                'paths' => [
                    'migrations' => ROOT_DIR . 'src/Migrations',
                ],
                'environments' => [
                    'default_migration_table' => 'migrations',
                    'default_database'        => 'php-censor',
                    'php-censor'              => [
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
            if (!\array_key_exists('pgsql-sslmode', $databaseSettings['servers']['write'][0])) {
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

        $buildFactory = new BuildFactory(
            $this->configuration,
            $buildStore
        );

        $buildService = new BuildService(
            $this->configuration,
            $buildFactory,
            $buildStore,
            $buildErrorStore,
            $projectStore
        );

        $logger = $this->initLogger($this->configuration);

        $this->add(new InstallCommand(
            $this->configuration,
            $this->databaseManager,
            $logger,
            $userStore,
            $projectGroupStore
        ));
        $this->add(new CreateAdminCommand(
            $this->configuration,
            $this->databaseManager,
            $logger,
            $userStore
        ));
        $this->add(new CreateBuildCommand(
            $this->configuration,
            $this->databaseManager,
            $logger,
            $projectStore,
            $buildService,
            $environmentStore
        ));
        $this->add(new RemoveOldBuildsCommand(
            $this->configuration,
            $this->databaseManager,
            $logger,
            $projectStore,
            $buildService
        ));
        $this->add(new WorkerCommand(
            $this->configuration,
            $this->databaseManager,
            $buildMetaStore,
            $buildErrorStore,
            $buildStore,
            $secretStore,
            $environmentStore,
            $logger,
            $buildService,
            $buildFactory
        ));
        $this->add(new RebuildQueueCommand(
            $this->configuration,
            $this->databaseManager,
            $logger,
            $buildStore,
            $buildErrorStore,
            $projectStore
        ));
        $this->add(new CheckLocalizationCommand(
            $this->configuration,
            $this->databaseManager,
            $logger
        ));
    }

    public function getHelp(): string
    {
        return self::LOGO . parent::getHelp();
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion(): string
    {
        return \sprintf('<info>%s</info> v%s', $this->getName(), $this->getVersion());
    }
}

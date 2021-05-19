<?php

declare(strict_types = 1);

namespace PHPCensor\Command;

use DateTime;
use Exception;
use PDO;
use Pheanstalk\Pheanstalk;
use PHPCensor\Command\Action\CreateAdmin;
use PHPCensor\Configuration;
use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Exception\RuntimeException;
use PHPCensor\Model\ProjectGroup;
use PHPCensor\Store\Factory;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\Store\UserStore;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Dumper;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @author Dan Cryer <dan@block8.co.uk>
 */
class InstallCommand extends Command
{
    protected string $configPath = APP_DIR . 'config.yml';

    protected function configure()
    {
        $this
            ->setName('php-censor:install')

            ->addOption(
                'url',
                null,
                InputOption::VALUE_OPTIONAL,
                'PHP Censor installation URL'
            )
            ->addOption('db-type', null, InputOption::VALUE_OPTIONAL, 'Database type')
            ->addOption('db-host', null, InputOption::VALUE_OPTIONAL, 'Database host')
            ->addOption('db-port', null, InputOption::VALUE_OPTIONAL, 'Database port')
            ->addOption('db-name', null, InputOption::VALUE_OPTIONAL, 'Database name')
            ->addOption('db-user', null, InputOption::VALUE_OPTIONAL, 'Database user')
            ->addOption(
                'db-password',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database password'
            )
            ->addOption(
                'db-pgsql-sslmode',
                null,
                InputOption::VALUE_OPTIONAL,
                'Postgres SSLMODE option'
            )
            ->addOption('admin-name', null, InputOption::VALUE_OPTIONAL, 'Admin name')
            ->addOption(
                'admin-password',
                null,
                InputOption::VALUE_OPTIONAL,
                'Admin password'
            )
            ->addOption('admin-email', null, InputOption::VALUE_OPTIONAL, 'Admin email')
            ->addOption(
                'queue-host',
                null,
                InputOption::VALUE_OPTIONAL,
                'Beanstalkd queue server hostname'
            )
            ->addOption(
                'queue-port',
                null,
                InputOption::VALUE_OPTIONAL,
                'Beanstalkd queue server port'
            )
            ->addOption(
                'queue-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Beanstalkd queue name'
            )
            ->addOption(
                'config-from-file',
                null,
                InputOption::VALUE_OPTIONAL,
                'Take config from file and ignore options',
                false
            )

            ->setDescription('Install PHP Censor');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFromFile = (bool)$input->getOption('config-from-file');

        if (!$configFromFile && !$this->verifyNotInstalled($output)) {
            return;
        }

        $output->writeln('');
        $output->writeln('<info>***************************************</info>');
        $output->writeln('<info>*  Welcome to PHP Censor installation *</info>');
        $output->writeln('<info>***************************************</info>');
        $output->writeln('');

        $this->checkRequirements($output);

        if (!$configFromFile) {
            $output->writeln('');
            $output->writeln('Please answer the following questions:');
            $output->writeln('--------------------------------------');
            $output->writeln('');

            $connectionVerified = false;
            while (!$connectionVerified) {
                $db                 = $this->getDatabaseInformation($input, $output);
                $connectionVerified = $this->verifyDatabaseDetails($db, $output);
            }
            $output->writeln('');

            $conf                           = [];
            $conf['php-censor']             = $this->getConfigInformation($input, $output);
            $conf['php-censor']['database'] = $db;

            $this->writeConfigFile($conf);
        }

        $this->reloadConfig();
        if (!$this->setupDatabase($output)) {
            return false;
        }

        $admin = $this->getAdminInformation($input, $output);
        $this->createAdminUser($admin, $input, $output);

        $this->createDefaultGroup($output);
    }

    private function verifyNotInstalled(OutputInterface $output): bool
    {
        if (\file_exists($this->configPath)) {
            $content = \file_get_contents($this->configPath);

            if (!empty($content)) {
                $output->writeln(
                    '<error>The PHP Censor config file exists and is not empty. ' .
                    'PHP Censor is already installed!</error>'
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Check PHP version, required modules and for disabled functions.
     *
     * @param  OutputInterface $output
     *
     * @throws Exception
     */
    private function checkRequirements(OutputInterface $output)
    {
        $output->writeln('Checking requirements...');
        $errors = false;

        if (!(\version_compare(PHP_VERSION, '7.4.0') >= 0)) {
            $output->writeln('');
            $output->writeln(
                '<error>PHP Censor requires at least PHP 7.4.0! Installed PHP ' . PHP_VERSION . '</error>');
            $errors = true;
        }

        $requiredExtensions = ['PDO', 'xml', 'json', 'curl', 'openssl'];

        foreach ($requiredExtensions as $extension) {
            if (!\extension_loaded($extension)) {
                $output->writeln('');
                $output->writeln('<error>Extension required: ' . $extension . '</error>');
                $errors = true;
            }
        }

        $requiredFunctions = ['exec', 'shell_exec', 'proc_open'];

        foreach ($requiredFunctions as $function) {
            if (!\function_exists($function)) {
                $output->writeln('');
                $output->writeln(
                    '<error>PHP Censor needs to be able to call the ' . $function .
                    '() function. Is it disabled in php.ini?</error>'
                );
                $errors = true;
            }
        }

        if ($errors) {
            throw new RuntimeException(
                'PHP Censor cannot be installed, as not all requirements are met. ' .
                'Please review the errors above before continuing.'
            );
        }

        $output->writeln('');
        $output->writeln('<info>OK</info>');
    }

    /**
     * Load information for admin user form CLI options or ask info to user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private function getAdminInformation(InputInterface $input, OutputInterface $output): array
    {
        /** @var $questionHelper QuestionHelper */
        $questionHelper = $this->getHelperSet()->get('question');

        /** @var UserStore $userStore */
        $userStore = Factory::getStore('User');

        $createAdmin = new CreateAdmin(
            $questionHelper,
            $input,
            $output,
            $userStore
        );

        return $createAdmin->process();
    }

    /**
     * Load configuration form CLI options or ask info to user.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     *
     * @throws Exception
     */
    private function getConfigInformation(InputInterface $input, OutputInterface $output): array
    {
        /** @var $helper QuestionHelper */
        $helper = $this->getHelperSet()->get('question');

        $urlValidator = function ($answer) {
            if (!\filter_var($answer, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException('Must be a valid URL.');
            }

            return \rtrim($answer, '/');
        };

        if ($url = $input->getOption('url')) {
            $url = $urlValidator($url);
        } else {
            $question = new Question(
                'Enter your application URL (default: "//php-censor.local"): ',
                '//php-censor.local'
            );
            $question->setValidator($urlValidator);
            $url = $helper->ask($input, $output, $question);
        }

        $queueConfig = $this->getQueueInformation($input, $output);

        return [
            'language' => 'en',
            'per_page' => 10,
            'url'      => $url,
            'queue'    => $queueConfig,
            'log'      => [
                'rotate'    => false,
                'max_files' => 0,
            ],
            'email_settings' => [
                'from_address'    => 'PHP Censor <no-reply@php-censor.local>',
                'smtp_address'    => null,
                'smtp_port'       => null,
                'smtp_username'   => null,
                'smtp_password'   => null,
                'smtp_encryption' => false,
            ],
            'ssh' => [
                'strength' => 2048,
                'comment'  => 'admin@php-censor',
            ],
            'bitbucket'   => [
                'username'     => null,
                'app_password' => null,
                'comments' => [
                    'commit'       => false,
                    'pull_request' => false,
                ],
                'status' => [
                    'commit' => false,
                ],
            ],
            'github'   => [
                'token'    => null,
                'comments' => [
                    'commit'       => false,
                    'pull_request' => false,
                ],
                'status' => [
                    'commit' => false,
                ],
            ],
            'build' => [
                'remove_builds'          => true,
                'writer_buffer_size'     => 500,
                'allow_public_artifacts' => false,
                'keep_builds'            => 100,
            ],
            'security' => [
                'disable_auth'    => false,
                'default_user_id' => 1,
                'auth_providers'  => [
                    'internal' => [
                        'type' => 'internal',
                    ],
                ],
            ],
            'dashboard_widgets' => [
                'all_projects' => [
                    'side' => 'left',
                ],
                'last_builds' => [
                    'side' => 'right',
                ],
            ],
        ];
    }

    /**
     * If the user wants to use a queue, get the necessary details.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    private function getQueueInformation(InputInterface $input, OutputInterface $output): array
    {
        $queueConfig = [
            'host'     => null,
            'port'     => Pheanstalk::DEFAULT_PORT,
            'name'     => null,
            'lifetime' => 600,
        ];

        $queueConfig['host'] = $input->getOption('queue-host');
        $queueConfig['name'] = $input->getOption('queue-name');

        $port = $input->getOption('queue-port');
        $queueConfig['port'] = $port ? $port : $queueConfig['port'];

        if (!$queueConfig['host'] && !$queueConfig['name']) {
            /** @var $helper QuestionHelper */
            $helper = $this->getHelper('question');

            $questionQueue = new Question(
                'Enter your queue hostname (default: "localhost"): ',
                'localhost'
            );
            $queueConfig['host'] = $helper->ask($input, $output, $questionQueue);

            $questionQueue = new Question(
                'Enter your queue port (default: ' . $queueConfig['port'] . '): ',
                $queueConfig['port']
            );
            $queueConfig['port'] = $helper->ask($input, $output, $questionQueue);

            $questionName = new Question(
                'Enter your queue name (default: "php-censor-queue"): ',
                'php-censor-queue'
            );
            $queueConfig['name'] = $helper->ask($input, $output, $questionName);
        }

        return $queueConfig;
    }

    /**
     * Load configuration for database form CLI options or ask info to user.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    private function getDatabaseInformation(InputInterface $input, OutputInterface $output): array
    {
        $db = [];

        /** @var $helper QuestionHelper */
        $helper = $this->getHelperSet()->get('question');

        if (!$dbType = $input->getOption('db-type')) {
            $questionType = new Question('Enter your database type ("mysql" or "pgsql"): ');
            $dbType       = \trim(\strtolower($helper->ask($input, $output, $questionType)));
        }

        if (!$dbHost = $input->getOption('db-host')) {
            $questionHost = new Question(
                'Enter your database host (default: "localhost"): ',
                'localhost'
            );
            $dbHost = \trim($helper->ask($input, $output, $questionHost));
        }

        $defaultPort = 3306;
        if ('pgsql' === $dbType) {
            $defaultPort = 5432;
        }

        $dbPort = $input->getOption('db-port');
        if (!$dbPort) {
            $questionPort = new Question(
                'Enter your database port (default: ' . $defaultPort . '): ',
                $defaultPort
            );

            $dbPort = (int)$helper->ask($input, $output, $questionPort);
        } elseif ('default' === $dbPort) {
            $dbPort = $defaultPort;
        } else {
            $dbPort = (int)$dbPort;
        }

        if (
            $dbType === 'pgsql'
            && !$dbPgsqlSslMode = $input->getOption('db-pgsql-sslmode')
        ) {
            $questionSslMode = new Question('Enter your database connection\'s SSL mode (default: prefer): ', 'prefer');
            $dbPgsqlSslMode  = $helper->ask($input, $output, $questionSslMode);
        }

        if (!$dbName = $input->getOption('db-name')) {
            $questionDb = new Question(
                'Enter your database name (default: "php-censor-db"): ',
                'php-censor-db'
            );
            $dbName = \trim($helper->ask($input, $output, $questionDb));
        }

        if (!$dbUser = $input->getOption('db-user')) {
            $questionUser = new Question(
                'Enter your database user (default: "php-censor-user"): ',
                'php-censor-user'
            );
            $dbUser = \trim($helper->ask($input, $output, $questionUser));
        }

        if (!$dbPass = $input->getOption('db-password')) {
            $questionPass = new Question('Enter your database password: ');
            $questionPass->setHidden(true);
            $questionPass->setHiddenFallback(false);
            $dbPass = $helper->ask($input, $output, $questionPass);
        }

        $dbServers  = [
            [
                'host' => $dbHost,
            ]
        ];

        if ($dbType === 'pgsql' && !empty($dbPgsqlSslMode)) {
            $dbServers[0]['pgsql-sslmode'] = $dbPgsqlSslMode;
        }

        $dbServers[0]['port'] = $dbPort;

        $db['servers']['read']  = $dbServers;
        $db['servers']['write'] = $dbServers;

        $db['type']     = $dbType;
        $db['name']     = $dbName;
        $db['username'] = $dbUser;
        $db['password'] = $dbPass;

        return $db;
    }

    /**
     * Try and connect to DB using the details provided
     *
     * @param  array           $db
     * @param  OutputInterface $output
     *
     * @return bool
     */
    private function verifyDatabaseDetails(array $db, OutputInterface $output): bool
    {
        $dns = $db['type'] . ':host=' . $db['servers']['write'][0]['host'];

        if (isset($db['servers']['write'][0]['port'])) {
            $dns .= ';port=' . (int)$db['servers']['write'][0]['port'];
        }

        $dns .= ';dbname=' . $db['name'];

        if ($db['type'] === 'pgsql') {
            $dns .= ';sslmode=' . $db['servers']['write'][0]['pgsql-sslmode'];
        }

        $pdoOptions = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT    => 2,
        ];
        if ('mysql' === $db['type']) {
            $pdoOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'UTF8'";
        }

        try {
            $pdo = new PDO(
                $dns,
                $db['username'],
                $db['password'],
                $pdoOptions
            );

            unset($pdo);

            return true;
        } catch (\Throwable $ex) {
            $output->writeln(
                '<error>PHP Censor could not connect to database with the details provided. ' .
                'Please try again.</error>'
            );
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
        }

        return false;
    }

    /**
     * Write the config.yml file.
     * @param array $config
     */
    private function writeConfigFile(array $config): void
    {
        $dumper = new Dumper();
        $yaml   = $dumper->dump($config, 4);

        file_put_contents($this->configPath, $yaml);
    }

    private function setupDatabase(OutputInterface $output): bool
    {
        $output->write('Setting up your database...');

        \exec(
            (ROOT_DIR . 'bin/console php-censor-migrations:migrate'),
            $outputMigration,
            $status
        );

        $output->writeln('');
        $output->writeln(\implode(PHP_EOL, $outputMigration));
        if (0 == $status) {
            $output->writeln('<info>OK</info>');

            return true;
        }

        $output->writeln('<error>Migration did not finish</error>');

        return false;
    }

    /**
     * Create admin user using information loaded before.
     */
    protected function createAdminUser(array $admin, InputInterface $input, OutputInterface $output): void
    {
        /** @var $questionHelper QuestionHelper */
        $questionHelper = $this->getHelperSet()->get('question');

        /** @var UserStore $userStore */
        $userStore = Factory::getStore('User');

        $createAdmin = new CreateAdmin(
            $questionHelper,
            $input,
            $output,
            $userStore
        );

        $createAdmin->create($admin);
    }

    /**
     * @param OutputInterface $output
     */
    protected function createDefaultGroup(OutputInterface $output): void
    {
        try {
            /** @var ProjectGroupStore $projectGroupStore */
            $projectGroupStore = Factory::getStore('ProjectGroup');
            $projectGroup      = $projectGroupStore->getByTitle('Projects');
            if ($projectGroup) {
                throw new RuntimeException('Default project group already exists!');
            }

            $group = new ProjectGroup();
            $group->setTitle('Projects');
            $group->setCreateDate(new DateTime());
            $group->setUserId(null);

            Factory::getStore('ProjectGroup')->save($group);

            $output->writeln('<info>Default project group created!</info>');
        } catch (\Throwable $ex) {
            $output->writeln('<error>PHP Censor failed to create default project group!</error>');
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
        }
    }

    protected function reloadConfig(): void
    {
        if (\file_exists($this->configPath)) {
            $config = new Configuration($this->configPath);
        }
    }
}

<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Command;

use Exception;
use PDO;

use b8\Config;
use b8\Store\Factory;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\ProjectGroup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCensor\Service\UserService;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Dumper;

/**
 * Install console command - Installs PHPCI.
 * 
 * @author     Dan Cryer <dan@block8.co.uk>
 * @package    PHPCI
 * @subpackage Console
 */
class InstallCommand extends Command
{
    protected $configFilePath;

    protected function configure()
    {
        $defaultPath = APP_DIR . 'config.yml';

        $this
            ->setName('php-censor:install')

            ->addOption('url', null, InputOption::VALUE_OPTIONAL, Lang::get('installation_url'))
            ->addOption('db-type', null, InputOption::VALUE_OPTIONAL, Lang::get('db_host'))
            ->addOption('db-host', null, InputOption::VALUE_OPTIONAL, Lang::get('db_host'))
            ->addOption('db-port', null, InputOption::VALUE_OPTIONAL, Lang::get('db_port'))
            ->addOption('db-name', null, InputOption::VALUE_OPTIONAL, Lang::get('db_name'))
            ->addOption('db-user', null, InputOption::VALUE_OPTIONAL, Lang::get('db_user'))
            ->addOption('db-pass', null, InputOption::VALUE_OPTIONAL, Lang::get('db_pass'))
            ->addOption('admin-name', null, InputOption::VALUE_OPTIONAL, Lang::get('admin_name'))
            ->addOption('admin-pass', null, InputOption::VALUE_OPTIONAL, Lang::get('admin_pass'))
            ->addOption('admin-mail', null, InputOption::VALUE_OPTIONAL, Lang::get('admin_email'))
            ->addOption('config-path', null, InputOption::VALUE_OPTIONAL, Lang::get('config_path'), $defaultPath)
            ->addOption('queue-use', null, InputOption::VALUE_OPTIONAL, 'Don\'t ask for queue details')
            ->addOption('queue-host', null, InputOption::VALUE_OPTIONAL, 'Beanstalkd queue server hostname')
            ->addOption('queue-name', null, InputOption::VALUE_OPTIONAL, 'Beanstalkd queue name')

            ->setDescription(Lang::get('install_app'));
    }

    /**
     * Installs PHPCI - Can be run more than once as long as you ^C instead of entering an email address.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configFilePath = $input->getOption('config-path');

        if (!$this->verifyNotInstalled($output)) {
            return;
        }

        $output->writeln('');
        $output->writeln('<info>******************</info>');
        $output->writeln('<info> '.Lang::get('welcome_to_app').'</info>');
        $output->writeln('<info>******************</info>');
        $output->writeln('');

        $this->checkRequirements($output);

        $output->writeln(Lang::get('please_answer'));
        $output->writeln('-------------------------------------');
        $output->writeln('');

        // ----
        // Get DB connection information and verify that it works:
        // ----
        $connectionVerified = false;

        while (!$connectionVerified) {
            $db = $this->getDatabaseInformation($input, $output);

            $connectionVerified = $this->verifyDatabaseDetails($db, $output);
        }

        $output->writeln('');

        $conf = [];
        $conf['b8']['database'] = $db;

        // ----
        // Get basic installation details (URL, etc)
        // ----
        $conf['php-censor'] = $this->getConfigInformation($input, $output);

        $this->writeConfigFile($conf);
        $this->setupDatabase($output);

        $admin = $this->getAdminInformation($input, $output);
        $this->createAdminUser($admin, $output);

        $this->createDefaultGroup($output);
    }

    /**
     * Check PHP version, required modules and for disabled functions.
     *
     * @param  OutputInterface $output
     * @throws \Exception
     */
    protected function checkRequirements(OutputInterface $output)
    {
        $output->write('Checking requirements...');
        $errors = false;

        // Check PHP version:
        if (!(version_compare(PHP_VERSION, '5.4.0') >= 0)) {
            $output->writeln('');
            $output->writeln('<error>'.Lang::get('app_php_req').'</error>');
            $errors = true;
        }

        // Check required extensions are present:
        $requiredExtensions = ['PDO'];

        foreach ($requiredExtensions as $extension) {
            if (!extension_loaded($extension)) {
                $output->writeln('');
                $output->writeln('<error>'.Lang::get('extension_required', $extension).'</error>');
                $errors = true;
            }
        }

        // Check required functions are callable:
        $requiredFunctions = ['exec', 'shell_exec'];

        foreach ($requiredFunctions as $function) {
            if (!function_exists($function)) {
                $output->writeln('');
                $output->writeln('<error>'.Lang::get('function_required', $function).'</error>');
                $errors = true;
            }
        }

        if (!function_exists('password_hash')) {
            $output->writeln('');
            $output->writeln('<error>'.Lang::get('function_required', $function).'</error>');
            $errors = true;
        }

        if ($errors) {
            throw new Exception(Lang::get('requirements_not_met'));
        }

        $output->writeln(' <info>'.Lang::get('ok').'</info>');
        $output->writeln('');
    }

    /**
     * Load information for admin user form CLI options or ask info to user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function getAdminInformation(InputInterface $input, OutputInterface $output)
    {
        $admin = [];

        /** @var $helper QuestionHelper */
        $helper = $this->getHelperSet()->get('question');

        // Function to validate mail address.
        $mailValidator = function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException(Lang::get('must_be_valid_email'));
            }

            return $answer;
        };

        if ($adminEmail = $input->getOption('admin-mail')) {
            $adminEmail = $mailValidator($adminEmail);
        } else {
            $questionEmail = new Question(Lang::get('enter_email'));
            $adminEmail    = $helper->ask($input, $output, $questionEmail);
        }

        if (!$adminName = $input->getOption('admin-name')) {
            $questionName = new Question(Lang::get('admin_name'));
            $adminName    = $helper->ask($input, $output, $questionName);
        }

        if (!$adminPass = $input->getOption('admin-pass')) {
            $questionPass = new Question(Lang::get('enter_password'));
            $questionPass->setHidden(true);
            $questionPass->setHiddenFallback(false);
            $adminPass    = $helper->ask($input, $output, $questionPass);
        }

        $admin['mail'] = $adminEmail;
        $admin['name'] = $adminName;
        $admin['pass'] = $adminPass;

        return $admin;
    }

    /**
     * Load configuration for PHPCI form CLI options or ask info to user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function getConfigInformation(InputInterface $input, OutputInterface $output)
    {
        $config = [];

        /** @var $helper QuestionHelper */
        $helper = $this->getHelperSet()->get('question');

        $urlValidator = function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_URL)) {
                throw new Exception(Lang::get('must_be_valid_url'));
            }

            return rtrim($answer, '/');
        };

        if ($url = $input->getOption('url')) {
            $url = $urlValidator($url);
        } else {
            $question = new Question(Lang::get('enter_app_url'));
            $question->setValidator($urlValidator);
            $url = $helper->ask($input, $output, $question);
        }

        $config['language'] = 'en';
        $config['per_page'] = 10;

        $config['url']   = $url;
        $config['queue'] = $this->getQueueInformation($input, $output);

        return $config;
    }

    /**
     * If the user wants to use a queue, get the necessary details.
     * 
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return array
     */
    protected function getQueueInformation(InputInterface $input, OutputInterface $output)
    {
        $skipQueueConfig = [
            'queue-use' => false,
            'host'      => null,
            'name'      => null,
            'lifetime'  => 600
        ];
        
        if (!$input->getOption('queue-use')) {
            return $skipQueueConfig;
        }

        $queueConfig = [
            'queue-use' => true,
        ];

        /** @var $helper QuestionHelper */
        $helper   = $this->getHelper('question');
        $question = new ConfirmationQuestion('Use beanstalkd to manage build queue? ', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<error>Skipping beanstalkd configuration.</error>');

            return $skipQueueConfig;
        }

        if (!$queueConfig['host'] = $input->getOption('queue-host')) {
            $questionQueue       = new Question('Enter your beanstalkd hostname [localhost]: ', 'localhost');
            $queueConfig['host'] = $helper->ask($input, $output, $questionQueue);
        }

        if (!$queueConfig['name'] = $input->getOption('queue-name')) {
            $questionName        = new Question('Enter the queue (tube) name to use [php-censor-queue]: ', 'php-censor-queue');
            $queueConfig['name'] = $helper->ask($input, $output, $questionName);
        }

        return $queueConfig;
    }

    /**
     * Load configuration for DB form CLI options or ask info to user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function getDatabaseInformation(InputInterface $input, OutputInterface $output)
    {
        $db = [];

        /** @var $helper QuestionHelper */
        $helper = $this->getHelperSet()->get('question');

        if (!$dbType = $input->getOption('db-type')) {
            $questionType = new Question(Lang::get('enter_db_type'), 'mysql');
            $dbType       = $helper->ask($input, $output, $questionType);
        }

        if (!$dbHost = $input->getOption('db-host')) {
            $questionHost = new Question(Lang::get('enter_db_host'), 'localhost');
            $dbHost       = $helper->ask($input, $output, $questionHost);
        }

        if (!$dbPort = $input->getOption('db-port')) {
            $questionPort = new Question(Lang::get('enter_db_port'), '3306');
            $dbPort       = $helper->ask($input, $output, $questionPort);
        }

        if (!$dbName = $input->getOption('db-name')) {
            $questionDb = new Question(Lang::get('enter_db_name'), 'php-censor-db');
            $dbName     = $helper->ask($input, $output, $questionDb);
        }

        if (!$dbUser = $input->getOption('db-user')) {
            $questionUser = new Question(Lang::get('enter_db_user'), 'php-censor-user');
            $dbUser       = $helper->ask($input, $output, $questionUser);
        }

        if (!$dbPass = $input->getOption('db-pass')) {
            $questionPass = new Question(Lang::get('enter_db_pass'));
            $questionPass->setHidden(true);
            $questionPass->setHiddenFallback(false);
            $dbPass = $helper->ask($input, $output, $questionPass);
        }

        $db['servers']['read']  = [[
            'host' => $dbHost,
            'port' => $dbPort,
        ]];
        $db['servers']['write'] = [[
            'host' => $dbHost,
            'port' => $dbPort,
        ]];
        $db['type']     = $dbType;
        $db['name']     = $dbName;
        $db['username'] = $dbUser;
        $db['password'] = $dbPass;

        return $db;
    }

    /**
     * Try and connect to DB using the details provided.
     * @param  array           $db
     * @param  OutputInterface $output
     * @return bool
     */
    protected function verifyDatabaseDetails(array $db, OutputInterface $output)
    {
        try {
            $pdo = new PDO(
                $db['type'] . ':host=' . $db['servers']['write'][0]['host'] . ';port=' . $db['servers']['write'][0]['host'] . 'dbname=' . $db['name'],
                $db['username'],
                $db['password'],
                [
                    \PDO::ATTR_PERSISTENT         => false,
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT            => 2,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                ]
            );

            unset($pdo);

            return true;

        } catch (Exception $ex) {
            $output->writeln('<error>'.Lang::get('could_not_connect').'</error>');
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
        }

        return false;
    }

    /**
     * Write the PHPCI config.yml file.
     * @param array $config
     */
    protected function writeConfigFile(array $config)
    {
        $dumper = new Dumper();
        $yaml   = $dumper->dump($config, 4);

        file_put_contents($this->configFilePath, $yaml);
    }

    protected function setupDatabase(OutputInterface $output)
    {
        $output->write(Lang::get('setting_up_db'));

        shell_exec(ROOT_DIR . 'bin/console php-censor-migrations:migrate');

        $output->writeln('<info>'.Lang::get('ok').'</info>');
    }

    /**
     * Create admin user using information loaded before.
     *
     * @param array $admin
     * @param OutputInterface $output
     */
    protected function createAdminUser($admin, $output)
    {
        try {
            $this->reloadConfig();

            $userStore = Factory::getStore('User');
            $userService = new UserService($userStore);
            $userService->createUser($admin['name'], $admin['mail'], $admin['pass'], 1);

            $output->writeln('<info>'.Lang::get('user_created').'</info>');
        } catch (\Exception $ex) {
            $output->writeln('<error>'.Lang::get('failed_to_create').'</error>');
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
        }
    }

    /**
     * @param OutputInterface $output
     */
    protected function createDefaultGroup($output)
    {
        try {
            $group = new ProjectGroup();
            $group->setTitle('Projects');

            Factory::getStore('ProjectGroup')->save($group);

            $output->writeln('<info>'.Lang::get('default_group_created').'</info>');
        } catch (\Exception $ex) {
            $output->writeln('<error>'.Lang::get('default_group_failed_to_create').'</error>');
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
        }
    }

    protected function reloadConfig()
    {
        $config = Config::getInstance();

        if (file_exists($this->configFilePath)) {
            $config->loadYaml($this->configFilePath);
        }
    }

    /**
     * @param OutputInterface $output
     * @return bool
     */
    protected function verifyNotInstalled(OutputInterface $output)
    {
        if (file_exists($this->configFilePath)) {
            $content = file_get_contents($this->configFilePath);

            if (!empty($content)) {
                $output->writeln('<error>'.Lang::get('config_exists').'</error>');
                $output->writeln('<error>'.Lang::get('update_instead').'</error>');
                return false;
            }
        }

        return true;
    }
}

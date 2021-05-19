<?php

declare(strict_types = 1);

namespace PHPCensor\Command;

use PHPCensor\Command\Action\CreateAdmin;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Store\UserStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 * @author Wogan May (@woganmay)
 */
class CreateAdminCommand extends Command
{
    protected UserStore $userStore;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        LoggerInterface $logger,
        UserStore $userStore,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $logger, $name);

        $this->userStore = $userStore;
    }

    protected function configure()
    {
        $this
            ->setName('php-censor:create-admin')

            ->addOption('admin-name', null, InputOption::VALUE_OPTIONAL, 'Admin name')
            ->addOption('admin-password', null, InputOption::VALUE_OPTIONAL, 'Admin password')
            ->addOption('admin-email', null, InputOption::VALUE_OPTIONAL, 'Admin email')

            ->setDescription('Create an admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $questionHelper QuestionHelper */
        $questionHelper = $this->getHelperSet()->get('question');

        $createAdmin = new CreateAdmin(
            $questionHelper,
            $input,
            $output,
            $this->userStore
        );

        $createAdmin->create(
            $createAdmin->process()
        );
    }
}

<?php

namespace PHPCensor\Command;

use PHPCensor\Service\UserService;
use PHPCensor\Store\UserStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

/**
 * Create admin command - creates an admin user
 * 
 * @author Wogan May (@woganmay)
 */
class CreateAdminCommand extends Command
{
    /**
     * @var UserStore
     */
    protected $userStore;

    /**
     * @param UserStore $userStore
     */
    public function __construct(UserStore $userStore)
    {
        parent::__construct();

        $this->userStore = $userStore;
    }

    protected function configure()
    {
        $this
            ->setName('php-censor:create-admin')

            ->addOption('admin-name',     null, InputOption::VALUE_OPTIONAL, 'Admin name')
            ->addOption('admin-password', null, InputOption::VALUE_OPTIONAL, 'Admin password')
            ->addOption('admin-email',    null, InputOption::VALUE_OPTIONAL, 'Admin email')

            ->setDescription('Create an admin user');
    }

    /**
     * Creates an admin user in the existing database
     *
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $helper QuestionHelper */
        $helper = $this->getHelperSet()->get('question');

        // Function to validate email address.
        $mailValidator = function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Must be a valid email address.');
            }

            return $answer;
        };

        if ($adminEmail = $input->getOption('admin-email')) {
            $adminEmail = $mailValidator($adminEmail);
        } else {
            $questionEmail = new Question('Admin email: ');
            $adminEmail    = $helper->ask($input, $output, $questionEmail);
        }

        if (!$adminName = $input->getOption('admin-name')) {
            $questionName = new Question('Admin name: ');
            $adminName    = $helper->ask($input, $output, $questionName);
        }

        if (!$adminPassword = $input->getOption('admin-password')) {
            $questionPassword = new Question('Admin password: ');
            $questionPassword->setHidden(true);
            $questionPassword->setHiddenFallback(false);
            $adminPassword = $helper->ask($input, $output, $questionPassword);
        }

        try {
            $userService = new UserService($this->userStore);
            $userService->createUser($adminName, $adminEmail, 'internal', json_encode(['type' => 'internal']), $adminPassword, true);

            $output->writeln('<info>User account created!</info>');
        } catch (\Exception $ex) {
            $output->writeln('<error>PHP Censor failed to create your admin account!</error>');
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
        }
    }
}

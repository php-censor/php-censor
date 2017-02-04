<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Command;

use PHPCensor\Service\UserService;
use PHPCensor\Helper\Lang;
use PHPCensor\Store\UserStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

/**
 * Create admin command - creates an admin user
 * 
 * @author     Wogan May (@woganmay)
 * @package    PHPCI
 * @subpackage Console
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
            ->setDescription(Lang::get('create_admin_user'));
    }

    /**
     * Creates an admin user in the existing PHPCI database
     *
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userService = new UserService($this->userStore);

        /** @var $helper QuestionHelper */
        $helper = $this->getHelperSet()->get('question');

        $question = new Question('Admin email: ');
        $question->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Must be a valid email address.');
            }

            return $answer;
        });
        $adminEmail = $helper->ask($input, $output, $question);

        $question  = new Question('Admin name: ');
        $adminName = $helper->ask($input, $output, $question);

        $question  = new Question('Admin password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $adminPass = $helper->ask($input, $output, $question);

        try {
            $userService->createUser($adminName, $adminEmail, 'default', json_encode(['type' => 'internal']), $adminPass, true);
            $output->writeln('<info>User account created!</info>');
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', 'PHP Censor failed to create your admin account.'));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}

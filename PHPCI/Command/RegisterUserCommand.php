<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Command;

use PHPCI\Service\UserService;
use PHPCI\Helper\Lang;
use PHPCI\Store\UserStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Register user command - creates an user with provider (Adirelle pluggable-auth)
 * @author       Dmitrii Zolotov (@itherz)
 * @package      PHPCI
 * @subpackage   Console
 */
class RegisterUserCommand extends Command
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
            ->setName('phpci:register-user')
            ->setDescription(Lang::get('register_user'));
    }

    /**
     * Creates an admin user in the existing PHPCI database
     *
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userService = new UserService($this->userStore);

        /** @var $dialog \Symfony\Component\Console\Helper\DialogHelper */
        $dialog = $this->getHelperSet()->get('dialog');

        // Function to validate mail address.
        $mailValidator = function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException(Lang::get('must_be_valid_email'));
            }

            return $answer;
        };

        $id = $dialog->ask($output, Lang::get('enter_id'));
        $pass = $dialog->askHiddenResponse($output, Lang::get('enter_password'));
	$email = $dialog->askAndValidate($output, Lang::get('enter_email'), $mailValidator, false);
	$providerKey = $dialog->ask($output, Lang::get('enter_providerkey'));
	$providerData = $dialog->ask($output, Lang::get('enter_providerdata'));
	$isAdmin = $dialog->ask($output, Lang::get('enter_isadmin'));
	$isAdmin = !empty($isAdmin);
        $name = $dialog->ask($output, Lang::get('enter_name'));

        try {
	    $userService->createUserWithProvider($name, $emailAddress, $id, $password, $providerKey, $providerData, $isAdmin = false);
            $output->writeln(Lang::get('user_created'));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', Lang::get('failed_to_create')));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}

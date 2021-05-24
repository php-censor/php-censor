<?php

declare(strict_types = 1);

namespace PHPCensor\Command\Action;

use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Service\UserService;
use PHPCensor\Store\UserStore;
use PHPCensor\StoreRegistry;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class CreateAdmin
{
    private QuestionHelper $questionHelper;

    private InputInterface $input;

    private OutputInterface $output;

    private StoreRegistry $storeRegistry;

    private UserStore $userStore;

    public function __construct(
        QuestionHelper $questionHelper,
        InputInterface $input,
        OutputInterface $output,
        StoreRegistry $storeRegistry,
        UserStore $userStore
    ) {
        $this->questionHelper = $questionHelper;
        $this->input          = $input;
        $this->output         = $output;
        $this->userStore      = $userStore;
        $this->storeRegistry  = $storeRegistry;
    }

    public function process(): array
    {
        $result = [];
        $mailValidator = function ($answer) {
            if (!\filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Must be a valid email address.');
            }

            return $answer;
        };

        if ($adminEmail = $this->input->getOption('admin-email')) {
            $adminEmail = $mailValidator($adminEmail);
        } else {
            $questionEmail = new Question('Admin email: ');
            $adminEmail    = $this->questionHelper->ask($this->input, $this->output, $questionEmail);
        }

        if (!$adminName = $this->input->getOption('admin-name')) {
            $questionName = new Question('Admin name: ');
            $adminName    = $this->questionHelper->ask($this->input, $this->output, $questionName);
        }

        if (!$adminPassword = $this->input->getOption('admin-password')) {
            $questionPassword = new Question('Admin password: ');
            $questionPassword->setHidden(true);
            $questionPassword->setHiddenFallback(false);
            $adminPassword = $this->questionHelper->ask($this->input, $this->output, $questionPassword);
        }

        $result['email']    = $adminEmail;
        $result['name']     = $adminName;
        $result['password'] = $adminPassword;

        return $result;
    }

    public function create(array $adminDetails): void
    {
        try {
            $adminUser = $this->userStore->getByEmail($adminDetails['email']);
            if ($adminUser) {
                throw new RuntimeException('Admin account already exists!');
            }

            $userService = new UserService($this->storeRegistry, $this->userStore);

            $userService->createUser(
                $adminDetails['name'],
                $adminDetails['email'],
                'internal',
                ['type' => 'internal'],
                $adminDetails['password'],
                true
            );

            $this->output->writeln('<info>User account created!</info>');
        } catch (\Throwable $ex) {
            $this->output->writeln('<error>PHP Censor failed to create your admin account!</error>');
            $this->output->writeln('<error>' . $ex->getMessage() . '</error>');
        }
    }
}

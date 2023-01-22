<?php

declare(strict_types=1);

namespace PHPCensor\Command;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Secret;
use PHPCensor\Store\SecretStore;
use PHPCensor\StoreRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCensor\Exception\HttpException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class AddSecretCommand extends Command
{
    private SecretStore $secretStore;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        LoggerInterface $logger,
        SecretStore $secretStore,
        ?string $name = null
    ) {
        parent::__construct($configuration, $databaseManager, $storeRegistry, $logger, $name);

        $this->secretStore = $secretStore;
    }

    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this
            ->setName('php-censor:add-secret')

            ->addArgument('secret-name', InputArgument::REQUIRED, 'Secret name')
            ->addArgument('secret-value', InputArgument::REQUIRED, 'Secret value')

            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force to update existing values', false)

            ->setDescription('Update secret');
    }

    /**
     * Loops through projects.
     *
     * @throws HttpException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $secretName  = $input->getArgument('secret-name');
        $secretValue = $input->getArgument('secret-value');
        $force       = (bool)$input->getOption('force');

        $secrets = $this->secretStore->getByNames([$secretName]);
        if ($secrets && !$force) {
            $output->writeln(
                '<error>Secret with name "%s" already exists! Use flag "-f|--force" if you want update secret.</error>'
            );

            return 1;
        }

        $secret = new Secret($this->storeRegistry);
        $secret->setCreateDate(new \DateTime());
        $secret->setUserId(null);
        $secret->setName($secretName);

        if ($force) {
            $secret = $secrets[0];
        }

        $secret->setValue($secretValue);

        $this->secretStore->save($secret);

        return 0;
    }
}

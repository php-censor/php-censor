<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Command;

use b8\Config;
use Monolog\Logger;
use PHPCensor\Helper\Lang;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate console command - Reads the database and generates models and stores.
 * @author       Dan Cryer <dan@block8.co.uk>
 * @package      PHPCI
 * @subpackage   Console
 */
class UpdateCommand extends Command
{
    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    public function __construct(Logger $logger, $name = null)
    {
        parent::__construct($name);
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('php-censor:update')
            ->setDescription(Lang::get('update_app'));
    }

    /**
     * Generates Model and Store classes by reading database meta data.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->verifyInstalled()) {
            return;
        }

        $output->write(Lang::get('updating_app'));

        shell_exec(ROOT_DIR . 'vendor/bin/phinx migrate -c "' . APP_DIR . 'phinx.php"');

        $output->writeln('<info>'.Lang::get('ok').'</info>');
    }

    protected function verifyInstalled()
    {
        $config = Config::getInstance();
        $url    = $config->get('php-censor.url');

        return !empty($url);
    }
}

<?php

namespace PHPCensor\Command;

use b8\Store\Factory;
use b8\HttpClient;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use PHPCensor\Model\Build;

/**
 * Run console command - Poll github for latest commit id
 * 
 * @author Jimmy Cleuren <jimmy.cleuren@gmail.com>
 */
class PollCommand extends Command
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
            ->setName('php-censor:poll-github')
            ->setDescription('Poll GitHub to check if we need to start a build.');
    }

    /**
     * Pulls all pending builds from the database and runs them.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new Parser();
        $yaml = file_get_contents(APP_DIR . 'config.yml');
        $this->settings = $parser->parse($yaml);

        $token = $this->settings['php-censor']['github']['token'];

        if (!$token) {
            $this->logger->error('No GitHub token found');
            return;
        }

        $buildStore = Factory::getStore('Build');

        $this->logger->addInfo('Finding projects to poll');
        $projectStore = Factory::getStore('Project');
        $result = $projectStore->getWhere();
        $this->logger->addInfo(sprintf('Found %d projects', count($result['items'])));

        foreach ($result['items'] as $project) {
            $http = new HttpClient('https://api.github.com');
            $commits = $http->get('/repos/' . $project->getReference() . '/commits', ['access_token' => $token]);

            $last_commit    = $commits['body'][0]['sha'];
            $last_committer = $commits['body'][0]['commit']['committer']['email'];
            $message        = $commits['body'][0]['commit']['message'];

            $this->logger->info(sprintf('Last commit to GitHub for %s is %s', $project->getTitle(), $last_commit));

            if (!$project->getArchived() && ($project->getLastCommit() != $last_commit && $last_commit != "")) {
                $this->logger->info('Last commit is different to database, adding new build.');

                $build = new Build();
                $build->setProjectId($project->getId());
                $build->setCommitId($last_commit);
                $build->setStatus(Build::STATUS_PENDING);
                $build->setBranch($project->getBranch());
                $build->setCreated(new \DateTime());
                $build->setCommitMessage($message);

                if (!empty($last_committer)) {
                    $build->setCommitterEmail($last_committer);
                }
                $buildStore->save($build);

                $project->setLastCommit($last_commit);
                $projectStore->save($project);
            }
        }

        $this->logger->addInfo('Finished processing builds.');
    }
}

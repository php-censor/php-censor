<?php

namespace PHPCensor\Helper;

use GuzzleHttp\Client;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * The Github Helper class provides some Github API call functionality.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Github
{
    private ConfigurationInterface $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Create a comment on a specific file (and commit) in a Github Pull Request.
     * @return null
     */
    public function createPullRequestComment($repo, $pullId, $commitId, $file, $line, $comment)
    {
        $token = $this->configuration->get('php-censor.github.token');

        if (!$token) {
            return null;
        }

        $url = '/repos/' . \strtolower($repo) . '/pulls/' . $pullId . '/comments';

        $params = [
            'body'      => $comment,
            'commit_id' => $commitId,
            'path'      => $file,
            'position'  => $line,
        ];

        $client = new Client();
        $client->post(('https://api.github.com' . $url), [
            'headers' => [
                'Authorization' => 'Basic ' . \base64_encode($token . ':x-oauth-basic'),
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'json' => $params,
        ]);
    }

    /**
     * Create a comment on a Github commit.
     * @return null
     */
    public function createCommitComment($repo, $commitId, $file, $line, $comment)
    {
        $token = $this->configuration->get('php-censor.github.token');

        if (!$token) {
            return null;
        }

        $url = '/repos/' . \strtolower($repo) . '/commits/' . $commitId . '/comments';

        $params = [
            'body'     => $comment,
            'path'     => $file,
            'position' => $line,
        ];

        $client = new Client();
        $client->post(('https://api.github.com' . $url), [
            'headers' => [
                'Authorization' => 'Basic ' . \base64_encode($token . ':x-oauth-basic'),
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'json' => $params,
        ]);
    }
}

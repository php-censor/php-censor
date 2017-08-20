<?php

namespace PHPCensor\Helper;

use b8\Config;
use GuzzleHttp\Client;

/**
 * The Bitbucket Helper class provides some Bitbucket API call functionality.
 */
class Bitbucket
{
    /**
     * Create a comment on a specific file (and commit) in a Bitbucket Pull Request.
     *
     * @param string $repo
     * @param int $pullId
     * @param string $commitId
     * @param string $file
     * @param int $line
     * @param string $comment
     *
     * @return null
     */
    public function createPullRequestComment($repo, $pullId, $commitId, $file, $line, $comment)
    {
        $username = Config::getInstance()->get('php-censor.bitbucket.username');
        $appPassword = Config::getInstance()->get('php-censor.bitbucket.app_password');

        if (empty($username) || empty($appPassword)) {
            return;
        }

        $url = '/1.0/repositories/' . $repo . '/pullrequests/' . $pullId . '/comments/';
        $client = new Client(['base_uri' => 'https://api.bitbucket.org']);
        $response = $client->post($url, [
            'auth'      => [$username, $appPassword],
            'headers'   => [
                'Content-Type' => 'application/json',
            ],
            'json'      => [
                'content'   => $comment,
                'anchor'    => substr($commitId, 0, 12),
                'filename'  => $file,
                'line_to'   => $line,
            ],
        ]);
    }

    /**
     * Create a comment on a Bitbucket commit.
     *
     * @param $repo
     * @param $commitId
     * @param $file
     * @param $line
     * @param $comment
     * @return null
     */
    public function createCommitComment($repo, $commitId, $file, $line, $comment)
    {
        $username = Config::getInstance()->get('php-censor.bitbucket.username');
        $appPassword = Config::getInstance()->get('php-censor.bitbucket.app_password');

        if (empty($username) || empty($appPassword)) {
            return;
        }

        $url = '/1.0/repositories/' . $repo . '/changesets/' . $commitId . '/comments';

        $client = new Client(['base_uri' => 'https://api.bitbucket.org']);
        $response = $client->post($url, [
            'auth'      => [$username, $appPassword],
            'headers'   => [
                'Content-Type' => 'application/json',
            ],
            'json'      => [
                'content'   => $comment,
                'filename'  => $file,
                'line_to'   => $line,
            ],
        ]);
    }

    /**
     * @param string $repo
     * @param int $pullRequestId
     *
     * @return string
     */
    public function getPullRequestDiff($repo, $pullRequestId)
    {
        $username = Config::getInstance()->get('php-censor.bitbucket.username');
        $appPassword = Config::getInstance()->get('php-censor.bitbucket.app_password');

        if (empty($username) || empty($appPassword)) {
            return;
        }

        $url = '/2.0/repositories/' . $repo . '/pullrequests/' . $pullRequestId . '/diff';

        $client = new Client(['base_uri' => 'https://api.bitbucket.org']);

        $response = $client->get($url, ['auth' => [$username, $appPassword]]);

        return (string)$response->getBody();
    }
}

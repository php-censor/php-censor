<?php

namespace PHPCensor\Helper;

use PHPCensor\Config;
use GuzzleHttp\Client;

/**
 * The Github Helper class provides some Github API call functionality.
 */
class Github
{
    /**
     * Create a comment on a specific file (and commit) in a Github Pull Request.
     * @param $repo
     * @param $pullId
     * @param $commitId
     * @param $file
     * @param $line
     * @param $comment
     * @return null
     */
    public function createPullRequestComment($repo, $pullId, $commitId, $file, $line, $comment)
    {
        $token = Config::getInstance()->get('php-censor.github.token');

        if (!$token) {
            return null;
        }

        $url = '/repos/' . strtolower($repo) . '/pulls/' . $pullId . '/comments';

        $params = [
            'body'      => $comment,
            'commit_id' => $commitId,
            'path'      => $file,
            'position'  => $line,
        ];

        $client = new Client();
        $client->post(('https://api.github.com' . $url), [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($token . ':x-oauth-basic'),
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'json' => $params,
        ]);
    }

    /**
     * Create a comment on a Github commit.
     * @param $repo
     * @param $commitId
     * @param $file
     * @param $line
     * @param $comment
     * @return null
     */
    public function createCommitComment($repo, $commitId, $file, $line, $comment)
    {
        $token = Config::getInstance()->get('php-censor.github.token');

        if (!$token) {
            return null;
        }

        $url = '/repos/' . strtolower($repo) . '/commits/' . $commitId . '/comments';

        $params = [
            'body'     => $comment,
            'path'     => $file,
            'position' => $line,
        ];

        $client = new Client();
        $client->post(('https://api.github.com' . $url), [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($token . ':x-oauth-basic'),
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'json' => $params,
        ]);
    }
}

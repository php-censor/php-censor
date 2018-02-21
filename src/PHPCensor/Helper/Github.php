<?php

namespace PHPCensor\Helper;

use b8\Config;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * The Github Helper class provides some Github API call functionality.
 */
class Github
{
    /**
     * Make all GitHub requests following the Link HTTP headers.
     *
     * @param string $url
     * @param mixed $params
     * @param array $results
     *
     * @return array
     */
    public function makeRecursiveRequest($url, $params, $results = [])
    {
        $client   = new Client();
        $response = $client->get(('https://api.github.com' . $url), [
            'query' => $params,
        ]);

        $body    = json_decode($response->getBody(), true);
        $headers = $response->getHeaders();

        foreach ($body as $item) {
            $results[] = $item;
        }

        foreach ($headers as $header_name => $header) {
            if (
                'Link' === $header_name &&
                preg_match('/^<([^>]+)>; rel="next"/', implode(', ', $header), $r)
            ) {
                $host = parse_url($r[1]);

                parse_str($host['query'], $params);
                $results = $this->makeRecursiveRequest($host['path'], $params, $results);

                break;
            }
        }

        return $results;
    }

    /**
     * Get an array of repositories from Github's API.
     */
    public function getRepositories()
    {
        $token = Config::getInstance()->get('php-censor.github.token');

        if (!$token) {
            return [];
        }

        $cache = new FilesystemCache('', 0, RUNTIME_DIR . 'cache');
        $rtn   = $cache->get('php-censor.github-repos');

        if (!$rtn) {
            $client   = new Client();
            $response = $client->get('https://api.github.com/user/orgs', [
                'query' => [
                    'access_token' => $token
                ],
            ]);

            $orgs = json_decode($response->getBody(), true);

            $params = ['type' => 'all', 'access_token' => $token];
            $repos  = ['user' => []];
            $repos['user'] = $this->makeRecursiveRequest('/user/repos', $params);

            foreach ($orgs as $org) {
                $repos[$org['login']] = $this->makeRecursiveRequest('/orgs/' . $org['login'] . '/repos', $params);
            }

            $rtn = [];
            foreach ($repos as $repoGroup) {
                foreach ($repoGroup as $repo) {
                    $rtn['repos'][] = $repo['full_name'];
                }
            }

            $cache->set('php-censor.github-repos', $rtn, 3600);
        }

        return $rtn;
    }

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

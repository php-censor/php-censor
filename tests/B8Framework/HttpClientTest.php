<?php

namespace Tests\b8;

use b8\HttpClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleRequest()
    {
        $http = new HttpClient();
        $html = $http->request('GET', 'https://www.google.com');

        $this->assertContains('Google', $html['body']);
    }

    public function testBaseUrl()
    {
        $http = new HttpClient('https://www.google.com');
        $html = $http->request('GET', '/');

        $this->assertContains('Google', $html['body']);
    }

    public function testGet()
    {
        $http = new HttpClient('https://www.google.com');
        $html = $http->get('overview', ['x' => 1]);

        $this->assertContains('Google', $html['body']);
    }

    public function testGetJson()
    {
        $http = new HttpClient('http://echo.jsontest.com');
        $data = $http->get('/key/value');

        $this->assertArrayHasKey('key', $data['body']);
    }

    public function testPost()
    {
        $http = new HttpClient('http://echo.jsontest.com');
        $data = $http->post('/key/value', ['test' => 'x']);

        $this->assertTrue(is_array($data));
    }

    public function testPut()
    {
        $http = new HttpClient('http://echo.jsontest.com');
        $data = $http->put('/key/value', ['test' => 'x']);

        $this->assertTrue(is_array($data));
    }

    public function testDelete()
    {
        $http = new HttpClient('http://echo.jsontest.com');
        $data = $http->delete('/key/value', ['test' => 'x']);

        $this->assertTrue(is_array($data));
    }
}
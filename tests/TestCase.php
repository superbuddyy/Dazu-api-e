<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** @var array */
    protected $headers = [];

    /**
     * Prepare a request and its headers.
     * Send the request.
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    protected function request(string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json($method, $uri, $data, array_merge($this->headers, $headers));
    }

    /**
     * Log in using the given data.
     * @param string $email
     * @param string $password
     * @return string
     */
    protected function login(string $email, string $password): string
    {
        $response = $this->json(
            'POST',
            route('auth.login'),
            ['email' => $email, 'password' => $password]
        );

        $data = json_decode($response->getContent());

        $this->headers = array_merge($this->headers, ['Authorization' => 'Bearer ' . $data->access_token]);

        return $data->access_token;
    }
}

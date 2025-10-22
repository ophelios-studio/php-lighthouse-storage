<?php namespace Lighthouse;

use GuzzleHttp\Client;
use Throwable;

class LighthouseClient
{
    private Client $http;
    private readonly Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->initializeClient();
    }

    /**
     * Performs a GET request to Lighthouse API using a full URL.
     *
     * @param string $url Full URL
     * @return array [statusCode, bodyString]
     * @throws LighthouseException
     */
    public function get(string $url): array
    {
        try {
            $response = $this->http->request('GET', $url);
            return [$response->getStatusCode(), (string) $response->getBody()];
        } catch (Throwable $e) {
            throw LighthouseException::fromThrowable($e, 'GET ' . $url);
        }
    }

    /**
     * Performs a multipart/form-data POST request (for file upload).
     *
     * @param string $url Full URL (upload endpoint is on a different host)
     * @param array<int,array<string,mixed>> $multipart
     * @return array [statusCode, bodyString]
     * @throws LighthouseException
     */
    public function postMultipart(string $url, array $multipart): array
    {
        try {
            $response = $this->http->request('POST', $url, [
                'multipart' => $multipart,
            ]);
            return [$response->getStatusCode(), (string) $response->getBody()];
        } catch (Throwable $e) {
            throw LighthouseException::fromThrowable($e, 'POST ' . $url);
        }
    }

    private function initializeClient(): void
    {
        $this->http = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->configuration->apiKey,
            ],
            'http_errors' => false,
            'timeout' => $this->configuration->timeout,
        ]);
    }
}

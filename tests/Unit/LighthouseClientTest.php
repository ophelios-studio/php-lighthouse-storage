<?php
namespace Unit;

use Lighthouse\Configuration;
use Lighthouse\LighthouseClient;
use Lighthouse\LighthouseException;
use PHPUnit\Framework\TestCase;

class LighthouseClientTest extends TestCase
{
    private function setHttp(LighthouseClient $client, object $http): void
    {
        $ref = new \ReflectionClass($client);
        $prop = $ref->getProperty('http');
        $prop->setAccessible(true);
        $prop->setValue($client, $http);
    }

    public function testGetWrapsExceptions(): void
    {
        $client = new LighthouseClient(new Configuration(apiKey: 'k'));
        $http = new class extends \GuzzleHttp\Client {
            public function __construct() {}
            public function request(string $method, $uri = '', array $options = []): \Psr\Http\Message\ResponseInterface
            {
                throw new \RuntimeException('boom');
            }
        };
        $this->setHttp($client, $http);
        $this->expectException(LighthouseException::class);
        $client->get('https://example');
    }

    public function testPostMultipartWrapsExceptions(): void
    {
        $client = new LighthouseClient(new Configuration(apiKey: 'k'));
        $http = new class extends \GuzzleHttp\Client {
            public function __construct() {}
            public function request(string $method, $uri = '', array $options = []): \Psr\Http\Message\ResponseInterface
            {
                throw new \RuntimeException('kapow');
            }
        };
        $this->setHttp($client, $http);
        $this->expectException(LighthouseException::class);
        $client->postMultipart('https://upload', []);
    }
}

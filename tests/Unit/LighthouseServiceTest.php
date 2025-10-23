<?php
namespace Unit;

use Lighthouse\Configuration;
use Lighthouse\LighthouseClient;
use Lighthouse\LighthouseProvider;
use Lighthouse\LighthouseService;
use PHPUnit\Framework\TestCase;

class LighthouseServiceTest extends TestCase
{
    public function testConstructWithApiKeyStringCreatesClient(): void
    {
        $service = new LighthouseService('apikey');
        $this->assertInstanceOf(LighthouseService::class, $service);
    }

    public function testDelegatesToProvider(): void
    {
        // Create a client double that records calls by overriding methods
        $client = new class(new Configuration(apiKey: 'k')) extends LighthouseClient {
            public array $calls = [];
            public function __construct(Configuration $c) { parent::__construct($c); }
            public function postMultipart(string $url, array $multipart): array { $this->calls[] = ['POST',$url]; return [200, json_encode(['Hash'=>'X'])]; }
            public function get(string $url): array { $this->calls[] = ['GET',$url]; return [200, json_encode(['data'=>['cid'=>'cid','fileSizeInBytes'=>'1','encryption'=>false,'fileName'=>'a','mimeType'=>'b','txHash'=>'']])]; }
        };
        $service = new LighthouseService($client);
        $cid = $service->uploadFile(__FILE__);
        $this->assertSame('X', $cid);
        $file = $service->getFileInfo('cid');
        $this->assertSame('cid', $file->cid);
        $url = LighthouseService::getFileUrl('cid');
        $this->assertSame('https://gateway.lighthouse.storage/ipfs/cid', $url);
        $this->assertCount(2, $client->calls);
        $this->assertSame('POST', $client->calls[0][0]);
        $this->assertSame('GET', $client->calls[1][0]);
    }
}

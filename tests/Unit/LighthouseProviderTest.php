<?php
namespace Unit;

use Lighthouse\LighthouseFile;
use Lighthouse\LighthouseProvider;
use Lighthouse\LighthouseException;
use PHPUnit\Framework\TestCase;

class LighthouseProviderTest extends TestCase
{
    private const UPLOAD = 'https://upload.lighthouse.storage/api/v0/add';
    private const INFO = 'https://api.lighthouse.storage/api/lighthouse/file_info?cid=abc';

    public function testUploadFileSuccess(): void
    {
        // create a temp file
        $tmp = tempnam(sys_get_temp_dir(), 'lh_');
        file_put_contents($tmp, 'content');

        $client = new class(new \Lighthouse\Configuration(apiKey: 'k')) extends \Lighthouse\LighthouseClient {
            public function __construct($c){parent::__construct($c);}    
            public function postMultipart(string $url, array $multipart): array { return [200, json_encode(['Hash'=>'cid123'])]; }
        };
        $provider = new LighthouseProvider($client);

        $cid = $provider->uploadFile($tmp, self::UPLOAD);
        $this->assertSame('cid123', $cid);

        @unlink($tmp);
    }

    public function testUploadFileNotFoundThrows(): void
    {
        $this->expectException(LighthouseException::class);
        $client = new class(new \Lighthouse\Configuration(apiKey: 'k')) extends \Lighthouse\LighthouseClient {
            public function __construct($c){parent::__construct($c);}    
        };
        $provider = new LighthouseProvider($client);
        $provider->uploadFile('/path/does/not/exist', self::UPLOAD);
    }

    public function testUploadFileNon2xxThrows(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'lh_');
        file_put_contents($tmp, 'content');
        $client = new class(new \Lighthouse\Configuration(apiKey: 'k')) extends \Lighthouse\LighthouseClient {
            public function __construct($c){parent::__construct($c);}    
            public function postMultipart(string $url, array $multipart): array { return [500, 'oops']; }
        };
        $provider = new LighthouseProvider($client);
        $this->expectException(LighthouseException::class);
        try {
            $provider->uploadFile($tmp, self::UPLOAD);
        } finally {
            @unlink($tmp);
        }
    }

    public function testUploadFileMalformedJsonThrows(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'lh_');
        file_put_contents($tmp, 'content');
        $client = new class(new \Lighthouse\Configuration(apiKey: 'k')) extends \Lighthouse\LighthouseClient {
            public function __construct($c){parent::__construct($c);}    
            public function postMultipart(string $url, array $multipart): array { return [200, '{not json']; }
        };
        $provider = new LighthouseProvider($client);
        $this->expectException(LighthouseException::class);
        try {
            $provider->uploadFile($tmp, self::UPLOAD);
        } finally {
            @unlink($tmp);
        }
    }

    public function testGetFileInfoReturnsNullOnNon200(): void
    {
        $client = new class(new \Lighthouse\Configuration(apiKey: 'k')) extends \Lighthouse\LighthouseClient {
            public function __construct($c){parent::__construct($c);}    
            public function get(string $url): array { return [404, '']; }
        };
        $provider = new LighthouseProvider($client);
        $this->assertNull($provider->getFileInfo(self::INFO));
    }

    public function testGetFileInfoSuccess(): void
    {
        $payload = [
            'data' => [
                'fileSizeInBytes' => '5',
                'cid' => 'abc',
                'encryption' => false,
                'fileName' => 'x',
                'mimeType' => 'text/plain',
                'txHash' => ''
            ]
        ];
        $client = new class(new \Lighthouse\Configuration(apiKey: 'k')) extends \Lighthouse\LighthouseClient {
            public function __construct($c){parent::__construct($c);}    
            public function get(string $url): array { return [200, json_encode(['data'=>['fileSizeInBytes'=>'5','cid'=>'abc','encryption'=>false,'fileName'=>'x','mimeType'=>'text/plain','txHash'=>'']])]; }
        };
        $provider = new LighthouseProvider($client);
        $file = $provider->getFileInfo(self::INFO);
        $this->assertInstanceOf(LighthouseFile::class, $file);
        $this->assertSame('abc', $file->cid);
        $this->assertSame(5, $file->fileSizeInBytes);
    }

    public function testGetFileUrlDefaultGateway(): void
    {
        $this->assertSame('https://gateway.lighthouse.storage/ipfs/abc', LighthouseProvider::getFileUrl('abc'));
    }
}

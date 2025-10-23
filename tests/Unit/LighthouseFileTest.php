<?php
namespace Unit;

use Lighthouse\LighthouseFile;
use PHPUnit\Framework\TestCase;

class LighthouseFileTest extends TestCase
{
    public function testBuildFromResponseWithJsonString(): void
    {
        $json = json_encode([
            'data' => [
                'fileSizeInBytes' => '123',
                'cid' => 'abc',
                'encryption' => false,
                'fileName' => 'name.txt',
                'mimeType' => 'text/plain',
                'txHash' => '0x',
            ]
        ]);
        $file = LighthouseFile::buildFromResponse($json);
        $this->assertInstanceOf(LighthouseFile::class, $file);
        $this->assertSame(123, $file->fileSizeInBytes);
        $this->assertSame('abc', $file->cid);
        $this->assertFalse($file->encryption);
        $this->assertSame('name.txt', $file->fileName);
        $this->assertSame('text/plain', $file->mimeType);
        $this->assertSame('0x', $file->txHash);
    }

    public function testBuildFromResponseWithArray(): void
    {
        $data = [
            'fileSizeInBytes' => '42',
            'cid' => 'cid42',
            'encryption' => true,
            'fileName' => 'file.png',
            'mimeType' => 'image/png',
            'txHash' => '',
        ];
        $file = LighthouseFile::buildFromResponse($data);
        $this->assertSame(42, $file->fileSizeInBytes);
        $this->assertSame('cid42', $file->cid);
        $this->assertTrue($file->encryption);
    }

    public function testBuildFromResponseReturnsNullOrDefaultsOnInvalid(): void
    {
        $this->assertNull(LighthouseFile::buildFromResponse(null));
        $this->assertNull(LighthouseFile::buildFromResponse('not json'));
        $obj = LighthouseFile::buildFromResponse(['unexpected' => 'structure']);
        $this->assertInstanceOf(LighthouseFile::class, $obj);
        $this->assertSame(0, $obj->fileSizeInBytes);
        $this->assertSame('', $obj->cid);
    }

    public function testGetUrlUsesProviderGateway(): void
    {
        $file = new LighthouseFile(1, 'mycid', false, 'a', 'b', 'c');
        // The URL comes from LighthouseProvider::getFileUrl which uses default constant
        $this->assertSame('https://gateway.lighthouse.storage/ipfs/mycid', $file->getUrl());
    }
}

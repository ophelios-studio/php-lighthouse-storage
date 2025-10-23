<?php namespace Integration;

use Lighthouse\LighthouseService;
use PHPUnit\Framework\TestCase;

class LiveLighthouseTest extends TestCase
{
    public function testLive(): void
    {
        $service = new LighthouseService($this->getApiKey());
        $file = $service->getFileInfo("bafkreih2ayd35c7a4xc2zqh5uma7uxkgfqs7uzarnwe5q7nul34ibmrchi");
        $this->assertNotNull($file);
        $this->assertSame("bafkreih2ayd35c7a4xc2zqh5uma7uxkgfqs7uzarnwe5q7nul34ibmrchi", $file->cid);
        $this->assertSame("ophelios-logo-only-black-gradient.png", $file->fileName);
        $this->assertSame(false, $file->encryption);
        $this->assertSame("image/png", $file->mimeType);
        $this->assertSame(813822, $file->fileSizeInBytes);
        $this->assertSame("https://gateway.lighthouse.storage/ipfs/bafkreih2ayd35c7a4xc2zqh5uma7uxkgfqs7uzarnwe5q7nul34ibmrchi", $file->getUrl());
    }

    private function getApiKey(): ?string
    {
        $candidates = [];
        $g = getenv('LIGHTHOUSE_API_KEY');
        if ($g !== false) {
            $candidates[] = $g;
        }
        if (isset($_ENV['LIGHTHOUSE_API_KEY'])) {
            $candidates[] = $_ENV['LIGHTHOUSE_API_KEY'];
        }
        if (isset($_SERVER['LIGHTHOUSE_API_KEY'])) {
            $candidates[] = $_SERVER['LIGHTHOUSE_API_KEY'];
        }
        foreach ($candidates as $val) {
            if (is_string($val)) {
                $val = trim($val);
                if ($val !== '') {
                    return $val;
                }
            }
        }
        return null;
    }
}

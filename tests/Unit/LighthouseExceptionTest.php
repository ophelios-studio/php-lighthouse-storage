<?php
namespace Unit;

use Lighthouse\LighthouseException;
use PHPUnit\Framework\TestCase;

class LighthouseExceptionTest extends TestCase
{
    public function testFromThrowableWraps(): void
    {
        $prev = new \RuntimeException('boom', 123);
        $ex = LighthouseException::fromThrowable($prev, 'GET http://x');
        $this->assertInstanceOf(LighthouseException::class, $ex);
        $this->assertStringContainsString('GET http://x: boom', $ex->getMessage());
        $this->assertSame(123, $ex->getCode());
        $this->assertSame($prev, $ex->getPrevious());
    }
}

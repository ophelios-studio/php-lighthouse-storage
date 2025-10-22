<?php namespace Lighthouse;

use Exception;
use Throwable;

/**
 * Unified exception type for the Lighthouse library. Wraps all internal and HTTP (Guzzle) errors.
 */
class LighthouseException extends Exception
{
    public static function fromThrowable(Throwable $throwable, ?string $context = null): self
    {
        $message = $context ? ($context . ': ' . $throwable->getMessage()) : $throwable->getMessage();
        return new self($message, $throwable->getCode(), $throwable);
    }
}

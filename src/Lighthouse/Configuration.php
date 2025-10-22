<?php namespace Lighthouse;

readonly class Configuration
{
    public function __construct(
        public string $apiKey,
        public float $timeout = 30.0,
    ) {}
}

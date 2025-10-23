# PHP Lighthouse Storage

[![Maintainability](https://qlty.sh/badges/061395ec-246e-4bf0-b3e8-3128e5efec27/maintainability.svg)](https://qlty.sh/gh/ophelios-studio/projects/php-lighthouse-storage)
[![Code Coverage](https://qlty.sh/badges/061395ec-246e-4bf0-b3e8-3128e5efec27/coverage.svg)](https://qlty.sh/gh/ophelios-studio/projects/php-lighthouse-storage)

A tiny PHP library to upload files and fetch file information from Lighthouse (Web3 storage on IPFS/Filecoin). Simple facade over Guzzle and a typed result object.

## ✨ Features
- Upload a file to Lighthouse and get back its CID (hash)
- Fetch file information by CID
- Build a public gateway URL for a CID
- Typed result: Lighthouse\LighthouseFile with common fields
- Guzzle-based HTTP client with unified LighthouseException errors

## 💿 Installation
Install with Composer:

```
composer require ophelios/php-lighthouse-storage
```

Requirements: PHP >= 8.4

## 🌱 Quick start
```php
use Lighthouse\LighthouseService;

$apiKey  = getenv('LIGHTHOUSE_API_KEY');
$service = new LighthouseService($apiKey);

// Get file information by CID
$cid  = 'bafkreih2ayd35c7a4xc2zqh5uma7uxkgfqs7uzarnwe5q7nul34ibmrchi';
$file = $service->getFileInfo($cid);

if ($file) {
    echo $file->fileName . " (" . $file->mimeType . ") => " . $file->fileSizeInBytes . " bytes\n";
    echo "Gateway URL: " . $file->getUrl() . "\n"; // convenience shortcut
}

// Upload a local file and get its CID
$cid = $service->uploadFile(__DIR__ . '/path/to/local-file.png');
echo "Uploaded CID: $cid\n";

// Build a public URL from a CID (static helper)
echo LighthouseService::getFileUrl($cid) . "\n";
```

## 📦 Result type: LighthouseFile
The library maps Lighthouse API JSON into a typed object with readonly properties:

- fileSizeInBytes (int)
- cid (string)
- encryption (bool)
- fileName (string)
- mimeType (string)
- txHash (string)

Helpers:
- getUrl(): returns the default gateway URL for this CID

Factory:
- LighthouseFile::buildFromResponse(string|array|null): ?LighthouseFile

## ⚙️ Custom client/configuration
If you need to control timeouts or inject your own client, you can pass a LighthouseClient built with a Configuration. The client wraps Guzzle and is already configured with your API key and JSON headers.

```php
use Lighthouse\Configuration;
use Lighthouse\LighthouseClient;
use Lighthouse\LighthouseService;

$cfg = new Configuration(
    apiKey: getenv('LIGHTHOUSE_API_KEY'),
    timeout: 30.0,
);
$client  = new LighthouseClient($cfg);
$service = new LighthouseService($client);
```

## 🧪 Testing
This repository includes unit tests and an optional live integration test.

Run all tests:
```
vendor/bin/phpunit
```

Run unit tests only:
```
vendor/bin/phpunit --testsuite Unit
```

Integration test requires a Lighthouse API key. You can place it in an .env file at the project root:
```
LIGHTHOUSE_API_KEY=your-key-here
```

Then run:
```
vendor/bin/phpunit --testsuite Integration
```

Tip: To generate coverage locally, enable Xdebug or pcov (CI already runs with coverage enabled).

## 🚨 Errors
All errors thrown by this library, including those originating from Guzzle, are wrapped in Lighthouse\\LighthouseException. Catch this exception to handle failures uniformly.

## 🤝 Contributing
- Open an issue for bugs or feature ideas
- Submit a PR with a clear description and tests when applicable

## 📄 License
MIT License © 2025 Ophelios. See LICENSE for details.

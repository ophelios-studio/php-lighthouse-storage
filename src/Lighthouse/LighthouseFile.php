<?php namespace Lighthouse;

readonly class LighthouseFile
{
    public function __construct(
        public int $fileSizeInBytes,
        public string $cid,
        public bool $encryption,
        public string $fileName,
        public string $mimeType,
        public string $txHash,
    ) {}

    public function getUrl(): string
    {
        return LighthouseProvider::getFileUrl($this->cid);
    }

    /**
     * Build an instance from the raw JSON string or decoded array returned by the API. Accepts either a JSON
     * string (the whole API response) or an associative array. Returns null when input is invalid or missing the
     * expected data structure.
     *
     * @param string|array|null $response
     * @return LighthouseFile|null
     */
    public static function buildFromResponse(string|array|null $response): ?self
    {
        if ($response === null) {
            return null;
        }

        $decoded = is_string($response) ? json_decode($response, true) : $response;
        if (!is_array($decoded)) {
            return null;
        }
        $data = $decoded['data'] ?? $decoded;
        if (!is_array($data) || empty($data)) {
            return null;
        }

        return new self(
            fileSizeInBytes: isset($data['fileSizeInBytes']) ? (int) $data['fileSizeInBytes'] : 0,
            cid: $data['cid'] ?? '',
            encryption: isset($data['encryption']) && $data['encryption'],
            fileName: $data['fileName'] ?? '',
            mimeType: $data['mimeType'] ?? '',
            txHash: $data['txHash'] ?? ''
        );
    }
}

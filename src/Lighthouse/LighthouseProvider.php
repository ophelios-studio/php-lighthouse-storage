<?php namespace Lighthouse;

class LighthouseProvider
{
    private const string API_URL = "https://api.lighthouse.storage/api/lighthouse/";
    private const string UPLOAD_URL = "https://upload.lighthouse.storage/api/v0/add";
    public const string GATEWAY_URL = "https://gateway.lighthouse.storage/ipfs/";

    public static function getFileUrl(string $cid): string
    {
        return self::GATEWAY_URL . $cid;
    }

    public function __construct(
        private readonly LighthouseClient $client
    ) {}

    /**
     * Upload a file to Lighthouse.
     * @param string $filePath
     * @return string CID (Hash)
     * @throws LighthouseException
     */
    public function uploadFile(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new LighthouseException("File not found: $filePath");
        }
        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($filePath, 'r'),
                'filename' => basename($filePath),
            ],
        ];

        [$status, $body] = $this->client->postMultipart(self::UPLOAD_URL, $multipart);

        if ($status < 200 || $status >= 300) {
            throw new LighthouseException("Upload failed with status $status: $body");
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded) || !isset($decoded['Hash'])) {
            throw new LighthouseException('Unexpected response from upload endpoint');
        }
        return $decoded['Hash'];
    }

    /**
     * Get file info from Lighthouse API.
     * @throws LighthouseException
     */
    public function getFileInfo(string $cid): ?LighthouseFile
    {
        $url = self::API_URL . 'file_info?cid=' . urlencode($cid);
        [$status, $body] = $this->client->get($url);
        if ($status !== 200 || $body === '') {
            return null;
        }
        return LighthouseFile::buildFromResponse($body);
    }
}

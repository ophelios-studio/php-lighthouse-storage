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

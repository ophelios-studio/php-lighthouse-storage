<?php namespace Lighthouse;

readonly class LighthouseService
{
    private LighthouseClient $client;

    public static function getFileUrl(string $cid): string
    {
        return LighthouseProvider::getFileUrl($cid);
    }

    public function __construct(string|LighthouseClient $client)
    {
        $this->client = ($client instanceof LighthouseClient)
            ? $client
            : new LighthouseClient(new Configuration(apiKey: $client));
    }

    /**
     * Uploads a file to Lighthouse.
     *
     * @param string $filePath The path to the file to upload.
     * @return string The CID (Hash) of the uploaded file.
     * @throws LighthouseException
     */
    public function uploadFile(string $filePath): string
    {
        $provider = new LighthouseProvider($this->client);
        return $provider->uploadFile($filePath);
    }

    /**
     * Retrieves the file information from Lighthouse.
     *
     * @param string $cid The CID (Hash) of the file.
     * @return LighthouseFile|null The file information.
     * @throws LighthouseException
     */
    public function getFileInfo(string $cid): ?LighthouseFile
    {
        $provider = new LighthouseProvider($this->client);
        return $provider->getFileInfo($cid);
    }
}

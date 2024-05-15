<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Traits\Conditionable;
use League\Flysystem\FilesystemOperator;
use OSS\OssClient;
use Zing\Flysystem\Oss\OssAdapter as Adapter;

/**
 * FilesystemAdapter for OSS
 */
class OssAdapter extends FilesystemAdapter
{
    use Conditionable;

    /**
     * @var \Zing\Flysystem\Oss\OssAdapter
     */
    protected $adapter;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        FilesystemOperator $driver,
        Adapter $adapter,
        array $config,
        protected OssClient $ossClient
    ) {
        parent::__construct($driver, $adapter, $config);
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param string $path
     */
    public function url($path): string
    {
        if (isset($this->config['url'])) {
            return $this->concatPathToUrl($this->config['url'], $this->prefixer->prefixPath($path));
        }

        $uri = new Uri($this->signUrl($this->prefixer->prefixPath($path), 0, []));

        return (string) $uri->withQuery('');
    }

    /**
     * Determine if temporary URLs can be generated.
     */
    public function providesTemporaryUrls(): bool
    {
        return true;
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param string $path
     * @param \DateTimeInterface $expiration
     * @param array<string, mixed> $options
     */
    public function temporaryUrl($path, $expiration, array $options = []): string
    {
        $uri = new Uri($this->signUrl($this->prefixer->prefixPath($path), $expiration, $options));

        if (isset($this->config['temporary_url'])) {
            $uri = $this->replaceBaseUrl($uri, $this->config['temporary_url']);
        }

        return (string) $uri;
    }

    /**
     * Get the underlying S3 client.
     */
    public function getClient(): OssClient
    {
        return $this->ossClient;
    }

    /**
     * Get a signed URL for the file at the given path.
     *
     * @param array<string, mixed> $options
     */
    public function signUrl(
        string $path,
        \DateTimeInterface|int $expiration,
        array $options = [],
        string $method = 'GET'
    ): string {
        $expires = $expiration instanceof \DateTimeInterface ? $expiration->getTimestamp() - time() : $expiration;

        return $this->ossClient->signUrl($this->config['bucket'], $path, $expires, $method, $options);
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param string $path
     * @param \DateTimeInterface $expiration
     * @param array<string, mixed> $options
     *
     * @return array{url: string, headers: never[]}
     */
    public function temporaryUploadUrl($path, $expiration, array $options = []): array
    {
        $uri = new Uri($this->signUrl($this->prefixer->prefixPath($path), $expiration, $options, 'PUT'));

        if (isset($this->config['temporary_url'])) {
            $uri = $this->replaceBaseUrl($uri, $this->config['temporary_url']);
        }

        return [
            'url' => (string) $uri,
            'headers' => [],
        ];
    }
}

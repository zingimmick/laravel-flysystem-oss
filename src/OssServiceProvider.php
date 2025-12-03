<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use League\Flysystem\Visibility;
use OSS\Credentials\StaticCredentialsProvider;
use OSS\OssClient;
use Zing\Flysystem\Oss\OssAdapter as Adapter;
use Zing\Flysystem\Oss\PortableVisibilityConverter;

/**
 * ServiceProvider for OSS.
 */
class OssServiceProvider extends ServiceProvider
{
    /**
     * Register the OSS driver creator Closure.
     */
    public function boot(): void
    {
        Storage::extend('oss', static function ($app, array $config): FilesystemAdapter {
            $root = $config['root'] ?? '';
            $options = $config['options'] ?? [];
            $portableVisibilityConverter = new PortableVisibilityConverter(
                $config['visibility'] ?? Visibility::PUBLIC,
                $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PUBLIC
            );
            $config['key'] ??= $config['access_key_id'] ?? null;
            $config['secret'] ??= $config['access_key_secret'] ?? null;
            $config['bucket_endpoint'] ??= $config['is_cname'] ?? false;
            $config['token'] ??= $config['security_token'] ?? null;
            $options = array_merge(
                $options,
                Arr::only($config, ['url', 'temporary_url', 'endpoint', 'bucket_endpoint'])
            );
            $optionMappings = [
                'region' => 'region',
                'endpoint' => 'endpoint',
                'bucket_endpoint' => 'cname',
                'signature_version' => 'signatureVersion',
                'http.proxy' => 'request_proxy',
            ];
            foreach ($optionMappings as $standardOption => $clientOption) {
                if (Arr::has($config, $standardOption)) {
                    $config[$clientOption] ??= Arr::get($config, $standardOption);
                }
            }

            if (class_exists(StaticCredentialsProvider::class)) {
                if (! isset($config['provider'])) {
                    $config['provider'] = new StaticCredentialsProvider(
                        $config['key'],
                        $config['secret'],
                        $config['token']
                    );
                }

                $ossClient = new OssClient($config);
            } else {
                $ossClient = new OssClient(
                    $config['key'],
                    $config['secret'],
                    $config['endpoint'],
                    $config['bucket_endpoint'],
                    $config['token'],
                    $config['proxy'] ?? null
                );
            }

            if (isset($config['retries'])) {
                $ossClient->setMaxTries($config['retries']);
            }

            if (isset($config['http']['timeout'])) {
                $ossClient->setTimeout($config['http']['timeout']);
            }

            if (isset($config['http']['connect_timeout'])) {
                $ossClient->setConnectTimeout($config['http']['connect_timeout']);
            }

            // Fix typo, will be removed in the next major version.
            if (isset($config['schema']) && ! isset($config['scheme'])) {
                $config['scheme'] = $config['schema'];
            }

            if (isset($config['scheme'])) {
                $ossClient->setUseSSL($config['scheme'] === 'https');
            }

            $ossAdapter = new Adapter(
                $ossClient,
                $config['bucket'],
                $root,
                $portableVisibilityConverter,
                null,
                $options
            );
            $adapter = $ossAdapter;
            if (($config['read-only'] ?? false) === true) {
                $adapter = new ReadOnlyFilesystemAdapter($adapter);
            }

            if (! empty($config['prefix'])) {
                $adapter = new PathPrefixedAdapter($adapter, $config['prefix']);
            }

            return new OssAdapter(new Filesystem($adapter, $config), $ossAdapter, $config, $ossClient);
        });
    }
}

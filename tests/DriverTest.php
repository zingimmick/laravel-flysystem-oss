<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Zing\Flysystem\Oss\OssAdapter;

/**
 * @internal
 */
final class DriverTest extends TestCase
{
    public function testDriverRegistered(): void
    {
        self::assertInstanceOf(OssAdapter::class, Storage::disk('oss')->getAdapter());
    }

    public function testUrl(): void
    {
        self::assertStringStartsWith('https://test-url', Storage::disk('oss-url')->url('test'));
    }

    public function testTemporaryUrl(): void
    {
        self::assertStringStartsWith(
            'https://test-temporary-url',
            Storage::disk('oss-temporary-url')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
    }

    public function testBucketEndpoint(): void
    {
        self::assertStringStartsWith('https://your-endpoint', Storage::disk('oss-bucket-endpoint')->url('test'));
    }

    public function testIsCname(): void
    {
        self::assertStringStartsWith(
            'https://your-endpoint',
            Storage::disk('oss-bucket-endpoint')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
        self::assertStringStartsWith(
            'https://your-endpoint',
            Storage::disk('oss-is-cname')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
    }

    public function testReadOnly(): void
    {
        $this->expectException(UnableToWriteFile::class);
        Storage::disk('oss-read-only')->write('test', 'test');
    }

    public function testPrefix(): void
    {
        self::assertSame(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-prefix-url')->url('test')
        );
        self::assertStringStartsWith(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-prefix-url')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
    }

    public function testReadOnlyAndPrefix(): void
    {
        self::assertSame(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-read-only-and-prefix-url')->url('test')
        );
        self::assertStringStartsWith(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-read-only-and-prefix-url')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
        $this->expectException(UnableToWriteFile::class);
        Storage::disk('oss-read-only-and-prefix-url')->write('test', 'test');
    }

    public function testTemporaryUploadUrl(): void
    {
        $now = Carbon::createFromTimestamp('1679168447');
        self::assertSame(
            [
                'url' => 'https://test-temporary-url/test?OSSAccessKeyId=aW52YWxpZC1rZXk%3D&Expires=1679168447&Signature=ac7W4XnraWI4g%2ForUC1AnYCVYFk%3D',
                'headers' => [],
            ],
            Storage::disk('oss-temporary-url')->temporaryUploadUrl('test', $now)
        );
    }
}

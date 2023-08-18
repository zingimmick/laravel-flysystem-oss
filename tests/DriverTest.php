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
        $this->assertInstanceOf(OssAdapter::class, Storage::disk('oss')->getAdapter());
    }

    public function testUrl(): void
    {
        $this->assertStringStartsWith('https://test-url', Storage::disk('oss-url')->url('test'));
    }

    public function testTemporaryUrl(): void
    {
        $this->assertStringStartsWith(
            'https://test-temporary-url',
            Storage::disk('oss-temporary-url')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
    }

    public function testBucketEndpoint(): void
    {
        $this->assertStringStartsWith('https://your-endpoint', Storage::disk('oss-bucket-endpoint')->url('test'));
    }

    public function testIsCname(): void
    {
        $this->assertStringStartsWith(
            'https://your-endpoint',
            Storage::disk('oss-bucket-endpoint')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
        $this->assertStringStartsWith(
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
        $this->assertSame(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-prefix-url')->url('test')
        );
        $this->assertStringStartsWith(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-prefix-url')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
    }

    public function testReadOnlyAndPrefix(): void
    {
        $this->assertSame(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-read-only-and-prefix-url')->url('test')
        );
        $this->assertStringStartsWith(
            'https://your-bucket.your-endpoint/root/prefix/test',
            Storage::disk('oss-read-only-and-prefix-url')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
        $this->expectException(UnableToWriteFile::class);
        Storage::disk('oss-read-only-and-prefix-url')->write('test', 'test');
    }

    public function testTemporaryUploadUrl(): void
    {
        $now = Carbon::createFromTimestamp('1679168447');
        $this->assertSame([
            'url' => 'https://test-temporary-url/test?OSSAccessKeyId=aW52YWxpZC1rZXk%3D&Expires=1679168447&Signature=ac7W4XnraWI4g%2ForUC1AnYCVYFk%3D',
            'headers' => [],
        ], Storage::disk('oss-temporary-url')->temporaryUploadUrl('test', $now));
    }
}

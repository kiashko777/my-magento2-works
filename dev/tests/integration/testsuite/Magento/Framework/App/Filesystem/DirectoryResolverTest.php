<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Filesystem;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for the \Magento\Framework\App\Filesystem\DirectoryResolver verification.
 */
class DirectoryResolverTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DirectoryResolver
     */
    private $directoryResolver;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @magentoAppIsolation enabled
     * @return void
     */
    public function testValidatePathWithException()
    {
        $this->expectException(FileSystemException::class);

        $directory = $this->filesystem
            ->getDirectoryWrite(DirectoryList::MEDIA);
        $path = $directory->getAbsolutePath();
        $this->directoryResolver->validatePath($path, 'wrong_dir');
    }

    /**
     * @param string $path
     * @param string $directoryConfig
     * @param bool $expectation
     * @dataProvider validatePathDataProvider
     * @return void
     * @throws FileSystemException
     * @magentoAppIsolation enabled
     */
    public function testValidatePathWithSymlink($path, $directoryConfig, $expectation)
    {
        $directory = $this->filesystem
            ->getDirectoryWrite(DirectoryList::PUB);
        $driver = $directory->getDriver();

        $mediaPath = $directory->getAbsolutePath('media');
        $mediaMovedPath = $directory->getAbsolutePath('moved-media');

        try {
            $driver->rename($mediaPath, $mediaMovedPath);
            $driver->symlink($mediaMovedPath, $mediaPath);
            $this->testValidatePath($path, $directoryConfig, $expectation);
        } finally {
            // be defensive in case some operations failed
            if ($driver->isExists($mediaPath) && $driver->isExists($mediaMovedPath)) {
                $driver->deleteFile($mediaPath);
                $driver->rename($mediaMovedPath, $mediaPath);
            } elseif ($driver->isExists($mediaMovedPath)) {
                $driver->rename($mediaMovedPath, $mediaPath);
            }
        }
    }

    /**
     * @param string $path
     * @param string $directoryConfig
     * @param bool $expectation
     * @dataProvider validatePathDataProvider
     * @return void
     * @throws FileSystemException
     * @magentoAppIsolation enabled
     */
    public function testValidatePath($path, $directoryConfig, $expectation)
    {
        $directory = $this->filesystem
            ->getDirectoryWrite(DirectoryList::MEDIA);
        $path = $directory->getAbsolutePath() . '/' . $path;
        $this->assertEquals($expectation, $this->directoryResolver->validatePath($path, $directoryConfig));
    }

    /**
     * @return array
     */
    public function validatePathDataProvider()
    {
        return [
            [
                '/',
                DirectoryList::MEDIA,
                true,
            ],
            [
                '/../../pub/',
                DirectoryList::MEDIA,
                false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->directoryResolver = $this->objectManager
            ->create(DirectoryResolver::class);
        $this->filesystem = $this->objectManager->create(Filesystem::class);
    }
}

<?php
/**
 * Test for \Magento\Framework\Filesystem
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList as AppDirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class FilesystemTest
 * Test for Magento\Framework\Filesystem class
 *
 */
class FilesystemTest extends TestCase
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Test getDirectoryRead method return valid instance
     */
    public function testGetDirectoryReadInstance()
    {
        $dir = $this->filesystem->getDirectoryRead(AppDirectoryList::VAR_DIR);
        $this->assertInstanceOf(Read::class, $dir);
    }

    /**
     * Test getDirectoryWrite method return valid instance
     */
    public function testGetDirectoryWriteInstance()
    {
        $dir = $this->filesystem->getDirectoryWrite(AppDirectoryList::VAR_DIR);
        $this->assertInstanceOf(Write::class, $dir);
    }

    /**
     * Test getUri returns right uri
     */
    public function testGetUri()
    {
        $this->assertStringContainsString(
            'media',
            $this->filesystem->getDirectoryRead(AppDirectoryList::MEDIA)->getAbsolutePath()
        );
    }

    protected function setUp(): void
    {
        $this->filesystem = Bootstrap::getObjectManager()->create(Filesystem::class);
    }
}

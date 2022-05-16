<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Backup;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @magentoAppIsolation enabled
     */
    public function testRollback()
    {
        $rootDir = Bootstrap::getInstance()->getAppTempDir()
            . '/rollback_test_' . time();
        $backupsDir = __DIR__ . '/_files/var/backups';
        $fileName = 'test.txt';

        mkdir($rootDir);

        $this->filesystem->setRootDir($rootDir)
            ->setBackupsDir($backupsDir)
            ->setTime(1474538269)
            ->setName('code')
            ->setBackupExtension('tgz');

        $this->assertTrue($this->filesystem->rollback());
        $this->assertFileExists($rootDir . '/' . $fileName);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->filesystem = $this->objectManager->create(Filesystem::class);
    }
}

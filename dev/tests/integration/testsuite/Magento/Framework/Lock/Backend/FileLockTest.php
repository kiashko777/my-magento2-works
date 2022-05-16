<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Lock\Backend;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * \Magento\Framework\Lock\Backend\File test case
 */
class FileLockTest extends TestCase
{
    /**
     * @var FileLock
     */
    private $model;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testLockAndUnlock()
    {
        $name = 'test_lock';

        $this->assertFalse($this->model->isLocked($name));

        $this->assertTrue($this->model->lock($name));
        $this->assertTrue($this->model->isLocked($name));
        $this->assertFalse($this->model->lock($name, 2));

        $this->assertTrue($this->model->unlock($name));
        $this->assertFalse($this->model->isLocked($name));
    }

    public function testUnlockWithoutExistingLock()
    {
        $name = 'test_lock';

        $this->assertFalse($this->model->isLocked($name));
        $this->assertFalse($this->model->unlock($name));
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(
            FileLock::class,
            ['path' => '/tmp']
        );
    }
}

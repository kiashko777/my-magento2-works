<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Lock\Backend;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * \Magento\Framework\Lock\Backend\Database test case.
 */
class DatabaseTest extends TestCase
{
    /**
     * @var Database
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
        $resourceConnection = $this->objectManager->create(ResourceConnection::class);
        $deploymentConfig = $this->objectManager->create(DeploymentConfig::class);
        // create object with new otherwise dummy locker is created because of di.xml preference for integration tests
        $this->model = new Database($resourceConnection, $deploymentConfig);
    }
}

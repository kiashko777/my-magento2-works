<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MessageQueue\Model\Plugin\ResourceModel;

use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\MessageQueue\Lock\ReaderInterface;
use Magento\Framework\MessageQueue\Lock\WriterInterface;
use Magento\Framework\MessageQueue\LockInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class LockTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var LockInterface
     */
    protected $lock;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * @var ReaderInterface
     */
    protected $reader;

    /**
     * Test to ensure Queue Lock Table is cleared when maintenance mode transitions from on to off.
     *
     * @return void
     */
    public function testLockClearedByMaintenanceModeOff()
    {
        /** @var $maintenanceMode MaintenanceMode */
        $maintenanceMode = $this->objectManager->get(MaintenanceMode::class);
        // md5() here is not for cryptographic use.
        // phpcs:ignore Magento2.Security.InsecureFunction
        $code = md5('consumer.name-1');
        $this->lock->setMessageCode($code);
        $this->writer->saveLock($this->lock);
        $this->reader->read($this->lock, $code);
        $id = $this->lock->getId();
        $maintenanceMode->set(true);
        $maintenanceMode->set(false);
        $this->reader->read($this->lock, $code);
        $emptyId = $this->lock->getId();

        $this->assertGreaterThanOrEqual('1', $id);
        $this->assertEmpty($emptyId);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->lock = $this->objectManager->get(LockInterface::class);
        $this->writer = $this->objectManager->get(WriterInterface::class);
        $this->reader = $this->objectManager->get(ReaderInterface::class);
    }
}

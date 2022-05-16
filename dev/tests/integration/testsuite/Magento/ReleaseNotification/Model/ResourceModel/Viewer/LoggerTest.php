<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ReleaseNotification\Model\ResourceModel\Viewer;

use Magento\ReleaseNotification\Model\Viewer\Log;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;
use Zend_Db_Statement_Exception;

/**
 * @magentoDbIsolation enabled
 */
class LoggerTest extends TestCase
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testLogAndGet()
    {
        $userModel = Bootstrap::getObjectManager()->get(User::class);
        $adminUserNameFromFixture = 'adminUser';
        $adminUserId = $userModel->loadByUsername($adminUserNameFromFixture)->getId();
        $this->assertEmpty($this->logger->get($adminUserId)->getId());
        $firstLogVersion = '2.2.2';
        $this->logger->log($adminUserId, $firstLogVersion);
        $firstLog = $this->logger->get($adminUserId);
        $this->assertInstanceOf(Log::class, $firstLog);
        $this->assertEquals($firstLogVersion, $firstLog->getLastViewVersion());
        $this->assertEquals($adminUserId, $firstLog->getViewerId());

        $secondLogVersion = '2.3.0';
        $this->logger->log($adminUserId, $secondLogVersion);
        $secondLog = $this->logger->get($adminUserId);
        $this->assertInstanceOf(Log::class, $secondLog);
        $this->assertEquals($secondLogVersion, $secondLog->getLastViewVersion());
        $this->assertEquals($adminUserId, $secondLog->getViewerId());
        $this->assertEquals($firstLog->getId(), $secondLog->getId());
    }

    /**
     */
    public function testLogNonExistUser()
    {
        $this->expectException(Zend_Db_Statement_Exception::class);

        $this->logger->log(200, '2.2.2');
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->logger = $objectManager->get(Logger::class);
    }
}

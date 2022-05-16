<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Model\Session;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Backend\Model\Session\AdminConfig.
 *
 * @magentoAppArea Adminhtml
 */
class AdminConfigTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function testConstructor()
    {
        $model = $this->objectManager->create(AdminConfig::class);
        $this->assertEquals('/index.php/backend', $model->getCookiePath());
    }

    /**
     * Test for setting session name for admin
     *
     */
    public function testSetSessionNameByConstructor()
    {
        $sessionName = 'adminHtmlSession';
        $adminConfig = $this->objectManager->create(
            AdminConfig::class,
            ['sessionName' => $sessionName]
        );
        $this->assertSame($sessionName, $adminConfig->getName());
    }

    protected function setUp(): void
    {
        parent::setUp();

        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);
        $this->objectManager = Bootstrap::getObjectManager();
    }
}

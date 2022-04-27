<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Model;

/**
 * Test class for \Magento\Backend\Model\Session.
 *
 * @magentoAppArea Adminhtml
 */
class SessionTest extends \PHPUnit\Framework\TestCase
{
    public function testContructor()
    {
        if (array_key_exists('Adminhtml', $_SESSION)) {
            unset($_SESSION['Adminhtml']);
        }
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Backend\Model\Session::class,
            [$logger]
        );
        $this->assertArrayHasKey('Adminhtml', $_SESSION);
    }
}

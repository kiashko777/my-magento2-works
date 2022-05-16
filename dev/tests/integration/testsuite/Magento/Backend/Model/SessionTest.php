<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test class for \Magento\Backend\Model\Session.
 *
 * @magentoAppArea Adminhtml
 */
class SessionTest extends TestCase
{
    public function testContructor()
    {
        if (array_key_exists('Adminhtml', $_SESSION)) {
            unset($_SESSION['Adminhtml']);
        }
        $logger = $this->createMock(LoggerInterface::class);
        Bootstrap::getObjectManager()->create(
            Session::class,
            [$logger]
        );
        $this->assertArrayHasKey('Adminhtml', $_SESSION);
    }
}

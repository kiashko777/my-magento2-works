<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Helper;

use Magento\Newsletter\Model\Subscriber;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Subscriber
     */
    protected $_subscriber;

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetConfirmationUrl()
    {
        $url = $this->_objectManager->get(
            Data::class
        )->getConfirmationUrl($this->_subscriber);
        $this->assertTrue(strpos($url, 'newsletter/subscriber/confirm') > 0);
        $this->assertFalse(strpos($url, 'admin'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetUnsubscribeUrl()
    {
        $url = $this->_objectManager->get(
            Data::class
        )->getUnsubscribeUrl($this->_subscriber);
        $this->assertTrue(strpos($url, 'newsletter/subscriber/unsubscribe') > 0);
        $this->assertFalse(strpos($url, 'admin'));
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_subscriber = $this->_objectManager->get(Subscriber::class);
    }
}

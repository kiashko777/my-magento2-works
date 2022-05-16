<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Account\Dashboard;

use Magento\Framework\View\LayoutInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    /**
     * @var Info
     */
    protected $_block;

    public function testGetSubscriptionObject()
    {
        $object = $this->_block->getSubscriptionObject();
        $this->assertInstanceOf(Subscriber::class, $object);

        $object2 = $this->_block->getSubscriptionObject();
        $this->assertSame($object, $object2);
    }

    protected function setUp(): void
    {
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Info::class
        );
    }
}

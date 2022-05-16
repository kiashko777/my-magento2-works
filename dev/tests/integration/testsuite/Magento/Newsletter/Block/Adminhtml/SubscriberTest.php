<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Block\Adminhtml;

use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class SubscriberTest extends TestCase
{
    public function testGetShowQueueAdd()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Subscriber */
        $block = $layout->createBlock(Subscriber::class, 'block');
        /** @var $childBlock \Magento\Framework\View\Element\Template */
        $childBlock = $layout->addBlock(\Magento\Framework\View\Element\Template::class, 'grid', 'block');

        $expected = 'test_data';
        $this->assertNotEquals($expected, $block->getShowQueueAdd());
        $childBlock->setShowQueueAdd($expected);
        $this->assertEquals($expected, $block->getShowQueueAdd());
    }
}

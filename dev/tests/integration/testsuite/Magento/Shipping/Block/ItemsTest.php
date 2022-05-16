<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Shipping\Block;

use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ItemsTest extends TestCase
{
    public function testGetCommentsHtml()
    {
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $block = $layout->createBlock(Items::class, 'block');
        $childBlock = $layout->addBlock(Text::class, 'shipment_comments', 'block');
        $shipment = Bootstrap::getObjectManager()->create(
            Shipment::class
        );

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getEntity());
        $this->assertEmpty($childBlock->getTitle());
        $this->assertNotEquals($expectedHtml, $block->getCommentsHtml($shipment));

        $childBlock->setText($expectedHtml);
        $actualHtml = $block->getCommentsHtml($shipment);
        $this->assertSame($shipment, $childBlock->getEntity());
        $this->assertNotEmpty($childBlock->getTitle());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}

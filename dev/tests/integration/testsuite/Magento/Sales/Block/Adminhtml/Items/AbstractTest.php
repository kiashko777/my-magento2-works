<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Block\Adminhtml\Items;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class AbstractTest extends TestCase
{
    public function testGetItemExtraInfoHtml()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block AbstractItems */
        $block = $layout->createBlock(AbstractItems::class, 'block');

        $item = new DataObject();

        $this->assertEmpty($block->getItemExtraInfoHtml($item));

        $expectedHtml = '<html><body>some data</body></html>';
        /** @var $childBlock Text */
        $childBlock = $layout->addBlock(
            Text::class,
            'other_block',
            'block',
            'order_item_extra_info'
        );
        $childBlock->setText($expectedHtml);

        $this->assertEquals($expectedHtml, $block->getItemExtraInfoHtml($item));
        $this->assertSame($item, $childBlock->getItem());
    }
}

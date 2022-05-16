<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Widget\Grid;

use Magento\Backend\Block\Widget\Grid\Massaction\Item;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ItemTest extends TestCase
{
    public function testGetAdditionalActionBlock()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Item */
        $block = $layout->createBlock(Item::class, 'block');
        $expected = $layout->addBlock(Template::class, 'additional_action', 'block');
        $this->assertSame($expected, $block->getAdditionalActionBlock());
    }
}

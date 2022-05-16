<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Wishlist\Block\Customer\Wishlist;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Wishlist\Block\Customer\Wishlist\Item\Column;
use PHPUnit\Framework\TestCase;

class ItemsTest extends TestCase
{
    public function testGetColumns()
    {
        $objectManager = Bootstrap::getObjectManager();
        $layout = $objectManager->get(
            LayoutInterface::class
        );
        $block = $layout->addBlock(Items::class, 'test');
        $child = $this->getMockBuilder(Column::class)
            ->setMethods(['isEnabled'])
            ->disableOriginalConstructor()
            ->getMock();

        $child->expects($this->any())->method('isEnabled')->willReturn(true);
        $layout->addBlock($child, 'child', 'test');
        $expected = $child->getType();
        $columns = $block->getColumns();
        $this->assertNotEmpty($columns);
        foreach ($columns as $column) {
            $this->assertSame($expected, $column->getType());
        }
    }
}

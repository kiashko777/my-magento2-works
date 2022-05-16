<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Wishlist\Block\Customer\Wishlist\Item\Options.
 */

namespace Magento\Wishlist\Block\Customer\Wishlist\Item;

use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    public function testGetTemplate()
    {
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Options::class
        );
        $this->assertEmpty($block->getTemplate());
        $product = new DataObject(['type_id' => 'test']);
        $item = new DataObject(['product' => $product]);
        $block->setItem($item);
        $this->assertNotEmpty($block->getTemplate());
        $block->setTemplate('template');
        $this->assertEquals('template', $block->getTemplate());
    }
}

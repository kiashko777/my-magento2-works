<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Checkout\Block\Cart
 */

namespace Magento\Checkout\Block;

use Magento\Framework\View\Element\Text;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    public function testGetMethods()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $child = $layout->createBlock(
            Text::class
        )->setChild(
            'child1',
            $layout->createBlock(
                Text::class,
                'method1'
            )
        )->setChild(
            'child2',
            $layout->createBlock(
                Text::class,
                'method2'
            )
        );
        /** @var $block Cart */
        $block = $layout->createBlock(Cart::class)->setChild('child', $child);
        $methods = $block->getMethods('child');
        $this->assertEquals(['method1', 'method2'], $methods);
    }

    public function testGetMethodsEmptyChild()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $childEmpty = $layout->createBlock(Text::class);
        /** @var $block Cart */
        $block = $layout->createBlock(Cart::class)->setChild('child', $childEmpty);
        $methods = $block->getMethods('child');
        $this->assertEquals([], $methods);
    }

    public function testGetMethodsNoChild()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Cart */
        $block = $layout->createBlock(Cart::class);
        $methods = $block->getMethods('child');
        $this->assertEquals([], $methods);
    }

    public function testGetPagerHtml()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Cart */
        $block = $layout->createBlock(Cart::class);
        $pager = $block->getPagerHtml();
        $this->assertEquals('', $pager);
    }
}

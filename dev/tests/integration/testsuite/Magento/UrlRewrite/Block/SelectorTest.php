<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Block;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\UrlRewrite\Block\Selector
 * @magentoAppArea Adminhtml
 */
class SelectorTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetModeUrl()
    {
        /** @var $layout LayoutInterface */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );

        /** @var $block Selector */
        $block = $layout->createBlock(Selector::class);

        $modeUrl = $block->getModeUrl('mode');
        $this->assertEquals(1, preg_match('/admin\/index\/index\/key\/[0-9a-zA-Z]+\/mode/', $modeUrl));
    }
}

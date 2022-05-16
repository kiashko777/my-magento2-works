<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Dhl\Block\Adminhtml;

use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class UnitofmeasureTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Unitofmeasure */
        $block = $layout->createBlock(Unitofmeasure::class);
        $this->assertNotEmpty($block->toHtml());
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type;

use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class SelectTest extends TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Select */
        $block = $layout->createBlock(
            Select::class,
            'select'
        );
        $html = $block->getPriceTypeSelectHtml();
        $this->assertStringContainsString('select_<%- data.select_id %>', $html);
        $this->assertStringContainsString('[<%- data.select_id %>]', $html);
    }
}

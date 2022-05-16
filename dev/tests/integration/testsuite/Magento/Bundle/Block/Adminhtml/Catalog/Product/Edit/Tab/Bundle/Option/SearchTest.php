<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea Adminhtml
     */
    public function testToHtmlHasIndex()
    {
        /** @var $layout LayoutInterface */
        $layout = Bootstrap::getObjectManager()->create(
            Layout::class
        );
        $block = $layout->createBlock(
            Search::class,
            'block2'
        );

        $indexValue = 'magento_index_set_to_test';
        $block->setIndex($indexValue);

        $html = $block->toHtml();
        $this->assertStringContainsString($indexValue, $html);
    }
}

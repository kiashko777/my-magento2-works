<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlHasOnClick()
    {
        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);
        /** @var $layout LayoutInterface */
        $layout = Bootstrap::getObjectManager()->create(
            Layout::class,
            ['area' => FrontNameResolver::AREA_CODE]
        );
        $block = $layout->createBlock(
            Grid::class,
            'block'
        );
        $block->setId('temp_id');

        $html = $block->toHtml();

        $regexpTemplate = '/\<script.*?\>.*?temp_id[^"]*\\.%s/is';
        $jsFuncs = ['doFilter', 'resetFilter'];
        foreach ($jsFuncs as $func) {
            $regexp = sprintf($regexpTemplate, $func);
            $this->assertMatchesRegularExpression($regexp, $html);
        }
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Category;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class TreeTest extends TestCase
{
    /** @var Tree */
    protected $_block;

    public function testGetSuggestedCategoriesJson()
    {
        $this->assertEquals(
            '[{"id":"2","children":[],"is_active":"1","label":"Default Category"}]',
            $this->_block->getSuggestedCategoriesJson('Default')
        );
        $this->assertEquals('[]', $this->_block->getSuggestedCategoriesJson(strrev('Default')));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_block = Bootstrap::getObjectManager()->create(
            Tree::class
        );

        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Tree::class,
            '',
            []
        );
    }
}

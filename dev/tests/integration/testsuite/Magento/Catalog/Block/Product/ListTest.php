<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Product;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Magento\Framework\App\State;
use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Block\Products\List.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 * @magentoAppArea frontend
 * @magentoDbIsolation disabled
 */
class ListTest extends TestCase
{
    /**
     * @var ListProduct
     */
    protected $_block;

    public function testGetLayer()
    {
        $this->assertInstanceOf(Layer::class, $this->_block->getLayer());
    }

    public function testGetLoadedProductCollection()
    {
        $this->_block->setShowRootCategory(true);
        $collection = $this->_block->getLoadedProductCollection();
        $this->assertInstanceOf(\Magento\Catalog\Model\ResourceModel\Product\Collection::class, $collection);
        /* Check that root category was defined for Layer as current */
        $this->assertEquals(2, $this->_block->getLayer()->getCurrentCategory()->getId());
    }

    /**
     * @covers \Magento\Catalog\Block\Product\ListProduct::getToolbarBlock
     * @covers \Magento\Catalog\Block\Product\ListProduct::getMode
     * @covers \Magento\Catalog\Block\Product\ListProduct::getToolbarHtml
     * @covers \Magento\Catalog\Block\Product\ListProduct::toHtml
     */
    public function testToolbarCoverage()
    {
        /** @var $parent ListProduct */
        $parent = $this->_getLayout()->createBlock(ListProduct::class, 'parent');

        /* Prepare toolbar block */
        $this->_getLayout()
            ->createBlock(Toolbar::class, 'product_list_toolbar');
        $parent->setToolbarBlockName('product_list_toolbar');

        $toolbar = $parent->getToolbarBlock();
        $this->assertInstanceOf(Toolbar::class, $toolbar, 'Default Toolbar');

        $parent->setChild('toolbar', $toolbar);
        /* In order to initialize toolbar collection block toHtml should be called before toolbar toHtml */
        $this->assertEmpty($parent->toHtml(), 'Block HTML'); /* Template not specified */
        $this->assertEquals('grid', $parent->getMode(), 'Default Mode'); /* default mode */
        $this->assertNotEmpty($parent->getToolbarHtml(), 'Toolbar HTML'); /* toolbar for one simple product */
    }

    protected function _getLayout()
    {
        return Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
    }

    public function testGetAdditionalHtmlEmpty()
    {
        $this->_block->setLayout($this->_getLayout());
        $this->assertEmpty($this->_block->getAdditionalHtml());
    }

    public function testGetAdditionalHtml()
    {
        $layout = $this->_getLayout();
        /** @var $parent ListProduct */
        $parent = $layout->createBlock(ListProduct::class);
        $childBlock = $layout->createBlock(
            Text::class,
            'test',
            ['data' => ['text' => 'test']]
        );
        $layout->setChild($parent->getNameInLayout(), $childBlock->getNameInLayout(), 'additional');
        $this->assertEquals('test', $parent->getAdditionalHtml());
    }

    public function testSetCollection()
    {
        $collection = Bootstrap::getObjectManager()
            ->create(Collection::class);
        $this->_block->setCollection($collection);
        $this->assertEquals($collection, $this->_block->getLoadedProductCollection());
    }

    public function testGetPriceBlockTemplate()
    {
        $this->assertNull($this->_block->getPriceBlockTemplate());
        $this->_block->setData('price_block_template', 'test');
        $this->assertEquals('test', $this->_block->getPriceBlockTemplate());
    }

    public function testPrepareSortableFieldsByCategory()
    {
        /** @var $category Category */
        $category = Bootstrap::getObjectManager()->create(
            Category::class
        );
        $category->setDefaultSortBy('name');
        $this->_block->prepareSortableFieldsByCategory($category);
        $this->assertEquals('name', $this->_block->getSortBy());
    }

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->get(State::class)
            ->setAreaCode('frontend');
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            ListProduct::class
        );
    }
}

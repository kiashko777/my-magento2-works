<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Block\Catalog\Category;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\UrlRewrite\Block\Catalog\Category\Tree
 *
 * @magentoAppArea Adminhtml
 */
class TreeTest extends TestCase
{
    /**
     * @var Tree
     */
    private $_treeBlock;

    /**
     * Test for method \Magento\UrlRewrite\Block\Catalog\Category\Tree::getTreeArray()
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/indexer_catalog_category.php
     * @magentoDataFixture Magento/Catalog/_files/indexer_catalog_products.php
     */
    public function testGetTreeArray()
    {
        $tree = $this->_treeBlock->getTreeArray();
        $this->assertFalse($tree['is_active']);
        $this->assertEquals('Root', (string)$tree['name']);
        $this->assertTrue($tree['expanded']);
        $this->assertCount(1, $tree['children']);
    }

    /**
     * Test that the getTreeArray() method scrubs single quotes and apostrophes from names
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/catalog_category_with_apostrophe.php
     */
    public function testGetTreeArrayApostropheReplaced()
    {
        $tree = $this->_treeBlock->getTreeArray();

        $this->assertStringNotContainsString('\'', $tree['children'][0]['children'][0]['children'][0]['name']);
        $this->assertEquals(
            '&#039;Category 6&#039;',
            $tree['children'][0]['children'][0]['children'][0]['name']
        );
    }

    /**
     * Test that the getTreeArray() method scrubs single quotes and apostrophes from names
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/catalog_category_with_doublequotes.php
     */
    public function testGetTreeArrayDoubleQuotesReplaced()
    {
        $tree = $this->_treeBlock->getTreeArray();

        $this->assertStringNotContainsString('\"', $tree['children'][0]['children'][0]['children'][0]['name']);
        $this->assertEquals(
            '&quot;Category 6&quot;',
            $tree['children'][0]['children'][0]['children'][0]['name']
        );
    }

    /**
     * Test prepare grid
     */
    public function testGetLoadTreeUrl()
    {
        $row = new DataObject(['id' => 1]);
        $this->assertStringStartsWith(
            'http://localhost/index.php',
            $this->_treeBlock->getLoadTreeUrl($row),
            'Tree load URL is invalid'
        );
    }

    /**
     * Test for method \Magento\UrlRewrite\Block\Catalog\Category\Tree::getCategoryCollection()
     */
    public function testGetCategoryCollection()
    {
        $collection = $this->_treeBlock->getCategoryCollection();
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_treeBlock = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Tree::class
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Helper;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CategoryTest
 * @package Magento\Catalog\Helper
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    protected $_helper;

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testGetStoreCategories()
    {
        $categories = $this->_helper->getStoreCategories();
        $this->assertInstanceOf(Collection::class, $categories);
        $index = 0;
        $expectedPaths = [
            [3, '1/2/3'],
            [6, '1/2/6'],
            [7, '1/2/7'],
            [9, '1/2/9'],
            [10, '1/2/10'],
            [11, '1/2/11'],
            [12, '1/2/12'],
        ];
        foreach ($categories as $category) {
            $this->assertInstanceOf(Node::class, $category);
            $this->assertEquals($expectedPaths[$index][0], $category->getId());
            $this->assertEquals($expectedPaths[$index][1], $category->getData('path'));
            $index++;
        }
    }

    public function testGetCategoryUrl()
    {
        $url = 'http://example.com/';
        $category = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class,
            ['data' => ['url' => $url]]
        );
        $this->assertEquals($url, $this->_helper->getCategoryUrl($category));

        $category = new DataObject(['url' => $url]);
        $this->assertEquals($url, $this->_helper->getCategoryUrl($category));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testCanShow()
    {
        // by ID of a category that is not a root
        $this->assertTrue($this->_helper->canShow(7));
    }

    public function testCanShowFalse()
    {
        /** @var $category \Magento\Catalog\Model\Category */
        $category = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        $this->assertFalse($this->_helper->canShow($category));
        $category->setId(1);
        $this->assertFalse($this->_helper->canShow($category));
        $category->setIsActive(true);
        $this->assertFalse($this->_helper->canShow($category));
    }

    public function testCanUseCanonicalTagDefault()
    {
        $this->assertEquals(0, $this->_helper->canUseCanonicalTag());
    }

    /**
     * @magentoConfigFixture current_store catalog/seo/category_canonical_tag 1
     */
    public function testCanUseCanonicalTag()
    {
        $this->assertEquals(1, $this->_helper->canUseCanonicalTag());
    }

    protected function setUp(): void
    {
        $this->_helper = Bootstrap::getObjectManager()->get(
            Category::class
        );
    }

    protected function tearDown(): void
    {
        if ($this->_helper) {
            $helperClass = get_class($this->_helper);
            /** @var $objectManager ObjectManager */
            $objectManager = Bootstrap::getObjectManager();
            $objectManager->get(Registry::class)->unregister('_helper/' . $helperClass);
        }
        $this->_helper = null;
    }
}

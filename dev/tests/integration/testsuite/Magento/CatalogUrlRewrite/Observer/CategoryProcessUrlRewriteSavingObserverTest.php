<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogUrlRewrite\Observer;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use PHPUnit\Framework\TestCase;

/**
 * Test Cases:
 *
 * - has changes for url_key, is_anchor, changed product list
 * - in global scope
 * - in store
 * - generate canonical
 * - generate children
 * - has children
 * - generate current
 * - has rewrites history
 * @magentoAppArea Adminhtml
 */
class CategoryProcessUrlRewriteSavingObserverTest extends TestCase
{
    /** @var ObjectManagerInterface */
    protected $objectManager;

    /**
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testUrlKeyHasChanged()
    {
        $categoryFilter = [
            UrlRewrite::ENTITY_TYPE => 'category',
        ];
        $expected = [
            [
                'request_path' => "category-1.html",
                'target_path' => "catalog/category/view/id/3",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "category-1/category-1-1.html",
                'target_path' => "catalog/category/view/id/4",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "category-1/category-1-1/category-1-1-1.html",
                'target_path' => "catalog/category/view/id/5",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "category-2.html",
                'target_path' => "catalog/category/view/id/6",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ]

        ];
        $actual = $this->getActualResults($categoryFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }
        /** @var Category $category */
        $category = $this->objectManager->create(Category::class);
        $category->load(3);
        $category->setData('save_rewrites_history', false);
        $category->setUrlKey('new-url');
        $category->save();
        $expected = [
            [
                'request_path' => "new-url.html",
                'target_path' => "catalog/category/view/id/3",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "new-url/category-1-1.html",
                'target_path' => "catalog/category/view/id/4",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "new-url/category-1-1/category-1-1-1.html",
                'target_path' => "catalog/category/view/id/5",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "category-2.html",
                'target_path' => "catalog/category/view/id/6",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ]

        ];
        $actual = $this->getActualResults($categoryFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }
    }

    /**
     * @param array $filter
     * @return array
     */
    private function getActualResults(array $filter)
    {
        /** @var UrlFinderInterface $urlFinder */
        $urlFinder = $this->objectManager->get(UrlFinderInterface::class);
        $actualResults = [];
        foreach ($urlFinder->findAllByData($filter) as $url) {
            $actualResults[] = [
                'request_path' => $url->getRequestPath(),
                'target_path' => $url->getTargetPath(),
                'is_auto_generated' => (int)$url->getIsAutogenerated(),
                'redirect_type' => $url->getRedirectType()
            ];
        }
        return $actualResults;
    }

    /**
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories_with_products.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testIsAnchorHasChanged()
    {
        $categoryFilter = [
            UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
        ];
        /** @var Category $category */
        $category = $this->objectManager->create(Category::class);
        $category->load(3);
        $category->setData('is_anchor', false);
        $category->save();
        $expected = [
            [
                'request_path' => "category-1.html",
                'target_path' => "catalog/category/view/id/3",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "category-1/category-1-1.html",
                'target_path' => "catalog/category/view/id/4",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "category-1/category-1-1/category-1-1-1.html",
                'target_path' => "catalog/category/view/id/5",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ],
            [
                'request_path' => "category-2.html",
                'target_path' => "catalog/category/view/id/6",
                'is_auto_generated' => 1,
                'redirect_type' => 0
            ]

        ];
        $actual = $this->getActualResults($categoryFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function tearDown(): void
    {
        $category = $this->objectManager->create(Category::class);
        $category->load(3);
        $category->delete();
    }
}

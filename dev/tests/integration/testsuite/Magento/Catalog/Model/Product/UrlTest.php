<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Products\Url.
 *
 * @magentoDataFixture Magento/Catalog/_files/url_rewrites.php
 * @magentoAppArea frontend
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UrlTest extends TestCase
{
    /**
     * @var Url
     */
    protected $_model;

    /**
     * @var ProductUrlPathGenerator
     */
    protected $urlPathGenerator;

    public function testGetUrlInStore()
    {
        $repository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );
        $product = $repository->get('simple');
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrlInStore($product));
    }

    /**
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @magentoConfigFixture default_store web/unsecure/base_url http://sample.com/
     * @magentoConfigFixture default_store web/unsecure/base_link_url http://sample.com/
     * @magentoConfigFixture fixturestore_store web/unsecure/base_url http://sample-second.com/
     * @magentoConfigFixture fixturestore_store web/unsecure/base_link_url http://sample-second.com/
     * @magentoDataFixture Magento/Catalog/_files/product_simple_multistore.php
     * @dataProvider getUrlsWithSecondStoreProvider
     * @magentoDbIsolation disabled
     * @magentoAppArea Adminhtml
     */
    public function testGetUrlInStoreWithSecondStore($storeCode, $expectedProductUrl)
    {
        $repository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );
        /** @var Store $store */
        $store = Bootstrap::getObjectManager()
            ->create(Store::class);
        $store->load($storeCode, 'code');
        /** @var Store $store */

        $product = $repository->get('simple');

        $this->assertEquals(
            $expectedProductUrl,
            $this->_model->getUrlInStore($product, ['_scope' => $store->getId(), '_nosid' => true])
        );
    }

    /**
     * @return array
     */
    public function getUrlsWithSecondStoreProvider()
    {
        return [
            'case1' => ['fixturestore', 'http://sample-second.com/index.php/simple-product-one.html'],
            'case2' => ['default', 'http://sample.com/index.php/simple-product-one.html']
        ];
    }

    /**
     * @magentoDbIsolation disabled
     */
    public function testGetProductUrl()
    {
        $repository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );
        $product = $repository->get('simple');
        $this->assertStringEndsWith('simple-product.html', $this->_model->getProductUrl($product));
    }

    public function testFormatUrlKey()
    {
        $this->assertEquals('abc-test', $this->_model->formatUrlKey('AbC#-$^test'));
    }

    public function testGetUrlPath()
    {
        /** @var $product Product */
        $product = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $product->setUrlPath('product.html');

        /** @var $category Category */
        $category = Bootstrap::getObjectManager()->create(
            Category::class,
            ['data' => ['url_path' => 'category', 'entity_id' => 5, 'path_ids' => [2, 3, 5]]]
        );
        $category->setOrigData();

        $this->assertEquals('product.html', $this->urlPathGenerator->getUrlPath($product));
        $this->assertEquals('category/product.html', $this->urlPathGenerator->getUrlPath($product, $category));
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppArea frontend
     */
    public function testGetUrl()
    {
        $repository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );
        $product = $repository->get('simple');
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrl($product));

        $product = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $product->setId(100);
        $this->assertStringContainsString('catalog/product/view/id/100/', $this->_model->getUrl($product));
    }

    /**
     * Check that rearranging product url rewrites do not influence on whether to use category in product links
     *
     * @magentoConfigFixture current_store catalog/seo/product_use_categories 0
     * @magentoDbIsolation disabled
     */
    public function testGetProductUrlWithRearrangedUrlRewrites()
    {
        $productRepository = Bootstrap::getObjectManager()->create(
            ProductRepository::class
        );
        $categoryRepository = Bootstrap::getObjectManager()->create(
            CategoryRepository::class
        );
        $registry = Bootstrap::getObjectManager()->get(
            Registry::class
        );
        $urlFinder = Bootstrap::getObjectManager()->create(
            UrlFinderInterface::class
        );
        $urlPersist = Bootstrap::getObjectManager()->create(
            UrlPersistInterface::class
        );

        $product = $productRepository->get('simple');
        $category = $categoryRepository->get($product->getCategoryIds()[0]);
        $registry->register('current_category', $category);
        $this->assertStringNotContainsString($category->getUrlPath(), $this->_model->getProductUrl($product));

        $rewrites = $urlFinder->findAllByData(
            [
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE
            ]
        );
        $this->assertGreaterThan(1, count($rewrites));
        foreach ($rewrites as $rewrite) {
            if ($rewrite->getRequestPath() === 'simple-product.html') {
                $rewrite->setUrlRewriteId($rewrite->getUrlRewriteId() + 1000);
            }
        }
        $urlPersist->replace($rewrites);
        $this->assertStringNotContainsString($category->getUrlPath(), $this->_model->getProductUrl($product));
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Url::class
        );
        $this->urlPathGenerator = Bootstrap::getObjectManager()->create(
            ProductUrlPathGenerator::class
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Attribute\Backend;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Observer\SwitchPriceAttributeScopeOnConfigChange;
use Magento\Directory\Model\ResourceModel\Currency;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Products\Attribute\Backend\Price.
 *
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PriceTest extends TestCase
{
    /**
     * @var Price
     */
    private $model;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        /** @var ReinitableConfigInterface $reinitiableConfig */
        $reinitiableConfig = Bootstrap::getObjectManager()->get(
            ReinitableConfigInterface::class
        );
        $reinitiableConfig->setValue(
            'catalog/price/scope',
            Store::PRICE_SCOPE_GLOBAL
        );
        $observer = Bootstrap::getObjectManager()->get(
            Observer::class
        );
        Bootstrap::getObjectManager()->get(SwitchPriceAttributeScopeOnConfigChange::class)
            ->execute($observer);
    }

    /**
     * @magentoDbIsolation disabled
     */
    public function testSetScopeDefault()
    {
        /* validate result of setAttribute */
        $this->assertEquals(
            ScopedAttributeInterface::SCOPE_GLOBAL,
            $this->model->getAttribute()->getIsGlobal()
        );
        $this->model->setScope($this->model->getAttribute());
        $this->assertEquals(
            ScopedAttributeInterface::SCOPE_GLOBAL,
            $this->model->getAttribute()->getIsGlobal()
        );
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoConfigFixture current_store catalog/price/scope 1
     */
    public function testSetScope()
    {
        $this->model->setScope($this->model->getAttribute());
        $this->assertEquals(
            ScopedAttributeInterface::SCOPE_WEBSITE,
            $this->model->getAttribute()->getIsGlobal()
        );
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoConfigFixture current_store catalog/price/scope 1
     * @magentoConfigFixture current_store currency/options/base GBP
     */
    public function testAfterSave()
    {
        /** @var Store $store */
        $store = $this->objectManager->create(Store::class);
        $globalStoreId = $store->load('admin')->getId();
        $product = $this->productRepository->get('simple');
        $product->setPrice('9.99');
        $product->setStoreId($globalStoreId);
        $product->getResource()->save($product);
        $product = $this->productRepository->get('simple', false, $globalStoreId, true);
        $this->assertEquals('9.990000', $product->getPrice());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoConfigFixture current_store catalog/price/scope 1
     * @magentoDbIsolation disabled
     * @magentoAppArea Adminhtml
     */
    public function testAfterSaveWithDifferentStores()
    {
        /** @var Store $store */
        $store = $this->objectManager->create(
            Store::class
        );
        $globalStoreId = $store->load('admin')->getId();
        $secondStoreId = $store->load('fixture_second_store')->getId();
        $thirdStoreId = $store->load('fixture_third_store')->getId();
        /** @var Action $productAction */
        $productAction = $this->objectManager->create(
            Action::class
        );

        $product = $this->productRepository->get('simple');
        $productId = $product->getId();
        $productAction->updateWebsites([$productId], [$store->load('fixture_second_store')->getWebsiteId()], 'add');
        $product->setStoreId($secondStoreId);
        $product->setPrice('9.99');
        $product->getResource()->save($product);

        $product = $this->productRepository->get('simple', false, $globalStoreId, true);
        $this->assertEquals(10, $product->getPrice());

        $product = $this->productRepository->get('simple', false, $secondStoreId, true);
        $this->assertEquals('9.990000', $product->getPrice());

        $product = $this->productRepository->get('simple', false, $thirdStoreId, true);
        $this->assertEquals('9.990000', $product->getPrice());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoConfigFixture current_store catalog/price/scope 1
     * @magentoDbIsolation disabled
     * @magentoAppArea Adminhtml
     */
    public function testAfterSaveWithSameCurrency()
    {
        /** @var Store $store */
        $store = $this->objectManager->create(
            Store::class
        );
        $globalStoreId = $store->load('admin')->getId();
        $secondStoreId = $store->load('fixture_second_store')->getId();
        $thirdStoreId = $store->load('fixture_third_store')->getId();
        /** @var Action $productAction */
        $productAction = $this->objectManager->create(
            Action::class
        );

        $product = $this->productRepository->get('simple');
        $productId = $product->getId();
        $productAction->updateWebsites([$productId], [$store->load('fixture_second_store')->getWebsiteId()], 'add');
        $product->setOrigData();
        $product->setStoreId($secondStoreId);
        $product->setPrice('9.99');
        $product->getResource()->save($product);

        $product = $this->productRepository->get('simple', false, $globalStoreId, true);
        $this->assertEquals(10, $product->getPrice());

        $product = $this->productRepository->get('simple', false, $secondStoreId, true);
        $this->assertEquals('9.990000', $product->getPrice());

        $product = $this->productRepository->get('simple', false, $thirdStoreId, true);
        $this->assertEquals('9.990000', $product->getPrice());
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppArea Adminhtml
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoConfigFixture current_store catalog/price/scope 1
     */
    public function testAfterSaveWithUseDefault()
    {
        /** @var Store $store */
        $store = $this->objectManager->create(
            Store::class
        );
        $globalStoreId = $store->load('admin')->getId();
        $secondStoreId = $store->load('fixture_second_store')->getId();
        $thirdStoreId = $store->load('fixture_third_store')->getId();
        /** @var Action $productAction */
        $productAction = $this->objectManager->create(
            Action::class
        );

        $product = $this->productRepository->get('simple');
        $productId = $product->getId();
        $productAction->updateWebsites([$productId], [$store->load('fixture_second_store')->getWebsiteId()], 'add');
        $product->setOrigData();
        $product->setStoreId($secondStoreId);
        $product->setPrice('9.99');
        $product->getResource()->save($product);

        $product = $this->productRepository->get('simple', false, $globalStoreId, true);
        $this->assertEquals(10, $product->getPrice());

        $product = $this->productRepository->get('simple', false, $secondStoreId, true);
        $this->assertEquals('9.990000', $product->getPrice());

        $product = $this->productRepository->get('simple', false, $thirdStoreId, true);
        $this->assertEquals('9.990000', $product->getPrice());

        $product->setStoreId($thirdStoreId);
        $product->setPrice(null);
        $product->getResource()->save($product);

        $product = $this->productRepository->get('simple', false, $globalStoreId, true);
        $this->assertEquals(10, $product->getPrice());

        $product = $this->productRepository->get('simple', false, $secondStoreId, true);
        $this->assertEquals(10, $product->getPrice());

        $product = $this->productRepository->get('simple', false, $thirdStoreId, true);
        $this->assertEquals(10, $product->getPrice());
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppArea Adminhtml
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoConfigFixture default_store catalog/price/scope 1
     */
    public function testAfterSaveForWebsitesWithDifferentCurrencies()
    {
        /** @var Store $store */
        $store = $this->objectManager->create(
            Store::class
        );

        /** @var Currency $rate */
        $rate = $this->objectManager->create(Currency::class);
        $rate->saveRates([
            'USD' => ['EUR' => 2],
            'EUR' => ['USD' => 0.5]
        ]);

        $globalStoreId = $store->load('admin')->getId();
        $secondStore = $store->load('fixture_second_store');
        $secondStoreId = $store->load('fixture_second_store')->getId();
        $thirdStoreId = $store->load('fixture_third_store')->getId();

        /** @var ReinitableConfigInterface $config */
        $config = $this->objectManager->get(MutableScopeConfigInterface::class);
        $config->setValue(
            'currency/options/default',
            'EUR',
            ScopeInterface::SCOPE_WEBSITES,
            'test'
        );

        $productAction = $this->objectManager->create(
            Action::class
        );
        $product = $this->productRepository->get('simple');
        $productId = $product->getId();
        $productAction->updateWebsites([$productId], [$secondStore->getWebsiteId()], 'add');
        $product->setOrigData();
        $product->setStoreId($globalStoreId);
        $product->setPrice(100);
        $product->getResource()->save($product);

        $product = $this->productRepository->get('simple', false, $globalStoreId, true);
        $this->assertEquals(100, $product->getPrice());

        $product = $this->productRepository->get('simple', false, $secondStoreId, true);
        $this->assertEquals(100, $product->getPrice());

        $product = $this->productRepository->get('simple', false, $thirdStoreId, true);
        $this->assertEquals(100, $product->getPrice());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        /** @var ReinitableConfigInterface $reinitiableConfig */
        $reinitiableConfig = $this->objectManager->get(ReinitableConfigInterface::class);
        $reinitiableConfig->setValue(
            'catalog/price/scope',
            Store::PRICE_SCOPE_WEBSITE
        );
        $observer = $this->objectManager->get(Observer::class);
        $this->objectManager->get(SwitchPriceAttributeScopeOnConfigChange::class)
            ->execute($observer);

        $this->model = $this->objectManager->create(
            Price::class
        );
        $this->productRepository = $this->objectManager->create(
            ProductRepositoryInterface::class
        );
        $this->model->setAttribute(
            $this->objectManager->get(
                Config::class
            )->getAttribute(
                'catalog_product',
                'price'
            )
        );
    }
}

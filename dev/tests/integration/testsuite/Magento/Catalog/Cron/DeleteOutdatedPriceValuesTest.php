<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Cron;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Observer\SwitchPriceAttributeScopeOnConfigChange;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeleteOutdatedPriceValuesTest extends TestCase
{
    /**
     * @var DeleteOutdatedPriceValues
     */
    private $cron;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoConfigFixture current_store catalog/price/scope 1
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     */
    public function testExecute()
    {
        $defaultStorePrice = 10.00;
        $secondStorePrice = 9.99;
        $secondStoreId = $this->store->load('fixture_second_store')->getId();
        /** @var Action $productAction */
        $productAction = $this->objectManager->create(
            Action::class
        );
        /** @var ReinitableConfigInterface $reinitiableConfig */
        $reinitiableConfig = $this->objectManager->get(ReinitableConfigInterface::class);
        $reinitiableConfig->setValue(
            'catalog/price/scope',
            Store::PRICE_SCOPE_WEBSITE
        );
        $observer = $this->objectManager->get(Observer::class);
        $this->objectManager->get(SwitchPriceAttributeScopeOnConfigChange::class)
            ->execute($observer);

        $reflection = new ReflectionClass(ScopeOverriddenValue::class);
        $paths = $reflection->getProperty('attributesValues');
        $paths->setAccessible(true);
        $paths->setValue($this->objectManager->get(ScopeOverriddenValue::class), null);
        $paths->setAccessible(false);

        $product = $this->productRepository->get('simple');
        $productResource = $this->objectManager->create(Product::class);

        $productId = $product->getId();
        $productAction->updateWebsites(
            [$productId],
            [$this->store->load('fixture_second_store')->getWebsiteId()],
            'add'
        );
        $product->setStoreId($secondStoreId);
        $product->setPrice($secondStorePrice);

        $productResource->save($product);
        $attribute = $this->objectManager->get(Config::class)
            ->getAttribute(
                'catalog_product',
                'price'
            );
        $this->assertEquals(
            $secondStorePrice,
            $productResource->getAttributeRawValue($productId, $attribute->getId(), $secondStoreId)
        );
        /** @var MutableScopeConfigInterface $config */
        $config = $this->objectManager->get(
            MutableScopeConfigInterface::class
        );

        $config->setValue(
            Store::XML_PATH_PRICE_SCOPE,
            null,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $this->cron->execute();
        $this->assertEquals(
            $secondStorePrice,
            $productResource->getAttributeRawValue($productId, $attribute->getId(), $secondStoreId)
        );

        $config->setValue(
            Store::XML_PATH_PRICE_SCOPE,
            Store::PRICE_SCOPE_GLOBAL,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        /** @var ScopeConfigInterface $scopeConfig */
        $this->cron->execute();
        $this->assertEquals(
            $defaultStorePrice,
            $productResource->getAttributeRawValue($productId, $attribute->getId(), $secondStoreId)
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $this->store = $this->objectManager->create(Store::class);
        $this->cron = $this->objectManager->create(DeleteOutdatedPriceValues::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        /** @var ReinitableConfigInterface $reinitiableConfig */
        $reinitiableConfig = $this->objectManager->get(ReinitableConfigInterface::class);
        $reinitiableConfig->setValue(
            'catalog/price/scope',
            Store::PRICE_SCOPE_GLOBAL
        );
        $observer = $this->objectManager->get(Observer::class);
        $this->objectManager->get(SwitchPriceAttributeScopeOnConfigChange::class)
            ->execute($observer);
    }
}

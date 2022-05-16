<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Helper;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Address;
use Magento\Framework\App\MutableScopeConfig;
use Magento\Framework\DataObject;
use Magento\Framework\Filter\Template;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\Config;
use Magento\Tax\Model\TaxRuleFixtureFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataTest extends TestCase
{
    /**
     * Tax helper
     *
     * @var Data
     */
    private $helper;

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Array of default tax classes ids
     *
     * Key is class name
     *
     * @var int[]
     */
    private $taxClasses;

    /**
     * Array of default tax rates ids.
     *
     * Key is rate percentage as string.
     *
     * @var int[]
     */
    private $taxRates;

    /**
     * Array of default tax rules ids.
     *
     * Key is rule code.
     *
     * @var int[]
     */
    private $taxRules;

    /**
     * Helps in creating required tax rules.
     *
     * @var TaxRuleFixtureFactory
     */
    private $taxRuleFixtureFactory;

    /**
     * @var MutableScopeConfig
     */
    private $scopeConfig;

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetBreadcrumbPath()
    {
        $category = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        $category->load(5);
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_category', $category);

        try {
            $path = $this->helper->getBreadcrumbPath();
            $this->assertIsArray($path);
            $this->assertEquals(['category3', 'category4', 'category5'], array_keys($path));
            $this->assertArrayHasKey('label', $path['category3']);
            $this->assertArrayHasKey('link', $path['category3']);
            $objectManager->get(Registry::class)->unregister('current_category');
        } catch (Exception $e) {
            $objectManager->get(Registry::class)->unregister('current_category');
            throw $e;
        }
    }

    public function testGetCategory()
    {
        $category = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_category', $category);
        try {
            $this->assertSame($category, $this->helper->getCategory());
            $objectManager->get(Registry::class)->unregister('current_category');
        } catch (Exception $e) {
            $objectManager->get(Registry::class)->unregister('current_category');
            throw $e;
        }
    }

    public function testGetProduct()
    {
        $product = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Product::class
        );
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_product', $product);
        try {
            $this->assertSame($product, $this->helper->getProduct());
            $objectManager->get(Registry::class)->unregister('current_product');
        } catch (Exception $e) {
            $objectManager->get(Registry::class)->unregister('current_product');
            throw $e;
        }
    }

    public function testSplitSku()
    {
        $sku = 'one-two-three';
        $this->assertEquals(['on', 'e-', 'tw', 'o-', 'th', 're', 'e'], $this->helper->splitSku($sku, 2));
    }

    public function testGetAttributeHiddenFields()
    {
        $this->assertEquals([], $this->helper->getAttributeHiddenFields());
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('attribute_type_hidden_fields', 'test');
        try {
            $this->assertEquals('test', $this->helper->getAttributeHiddenFields());
            $objectManager->get(Registry::class)->unregister('attribute_type_hidden_fields');
        } catch (Exception $e) {
            $objectManager->get(Registry::class)->unregister('attribute_type_hidden_fields');
            throw $e;
        }
    }

    public function testGetPriceScopeDefault()
    {
        // $this->assertEquals(\Magento\Store\Model\Store::PRICE_SCOPE_GLOBAL, $this->helper->getPriceScope());
        $this->assertNull($this->helper->getPriceScope());
    }

    /**
     * @magentoConfigFixture current_store catalog/price/scope 1
     */
    public function testGetPriceScope()
    {
        $this->assertEquals(Store::PRICE_SCOPE_WEBSITE, $this->helper->getPriceScope());
    }

    public function testIsPriceGlobalDefault()
    {
        $this->assertTrue($this->helper->isPriceGlobal());
    }

    /**
     * @magentoConfigFixture current_store catalog/price/scope 1
     */
    public function testIsPriceGlobal()
    {
        $this->assertFalse($this->helper->isPriceGlobal());
    }

    public function testIsUsingStaticUrlsAllowedDefault()
    {
        $this->assertFalse($this->helper->isUsingStaticUrlsAllowed());
    }

    /**
     * isUsingStaticUrlsAllowed()
     * setStoreId()
     * @magentoConfigFixture current_store cms/wysiwyg/use_static_urls_in_catalog 1
     */
    public function testIsUsingStaticUrlsAllowed()
    {
        $this->assertTrue($this->helper->isUsingStaticUrlsAllowed());
        $this->helper->setStoreId(
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getStore()->getId()
        );
        $this->assertTrue($this->helper->isUsingStaticUrlsAllowed());
    }

    public function testIsUrlDirectivesParsingAllowedDefault()
    {
        $this->assertTrue($this->helper->isUrlDirectivesParsingAllowed());
    }

    /**
     * isUrlDirectivesParsingAllowed()
     * setStoreId()
     * @magentoConfigFixture current_store catalog/frontend/parse_url_directives 0
     */
    public function testIsUrlDirectivesParsingAllowed()
    {
        $this->assertFalse($this->helper->isUrlDirectivesParsingAllowed());
        $this->helper->setStoreId(
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getStore()->getId()
        );
        $this->assertFalse($this->helper->isUrlDirectivesParsingAllowed());
    }

    public function testGetPageTemplateProcessor()
    {
        $this->assertInstanceOf(Template::class, $this->helper->getPageTemplateProcessor());
    }

    /**
     * @param DataObject $input
     * @param float $expectOutputPrice
     * @param string[] $configs
     * @param string $productClassName
     *
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @dataProvider getTaxPriceDataProvider
     */
    public function testGetTaxPrice(
        $input,
        $expectOutputPrice,
        $configs = [],
        $productClassName = 'DefaultProductClass'
    )
    {
        $this->setUpDefaultRules();
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $productRepository->get('simple');
        $product->setTaxClassId($this->taxClasses[$productClassName]);
        $shippingAddress = $this->getCustomerAddress();
        $billingAddress = $shippingAddress;
        foreach ($configs as $config) {
            $this->scopeConfig->setValue($config['path'], $config['value'], ScopeInterface::SCOPE_STORE, 'default');
        }

        $price = $this->helper->getTaxPrice(
            $product,
            $input->getPrice(),
            $input->getIncludingTax(),
            $shippingAddress,
            $billingAddress,
            $this->taxClasses['DefaultCustomerClass'],
            $input->getStore(),
            $input->getPriceIncludesTax(),
            $input->getRoundPrice()
        );
        if ($input->getNotEqual()) {
            $this->assertNotEquals($expectOutputPrice, $price);
        } else {
            $this->assertEquals($expectOutputPrice, $price);
        }
    }

    /**
     * Helper function that sets up some default rules
     */
    private function setUpDefaultRules()
    {
        $this->taxClasses = $this->taxRuleFixtureFactory->createTaxClasses([
            ['name' => 'DefaultCustomerClass', 'type' => ClassModel::TAX_CLASS_TYPE_CUSTOMER],
            ['name' => 'DefaultProductClass', 'type' => ClassModel::TAX_CLASS_TYPE_PRODUCT],
            ['name' => 'HigherProductClass', 'type' => ClassModel::TAX_CLASS_TYPE_PRODUCT],
        ]);

        $this->taxRates = $this->taxRuleFixtureFactory->createTaxRates([
            ['percentage' => 7.5, 'country' => 'US', 'region' => 42],
            ['percentage' => 7.5, 'country' => 'US', 'region' => 12], // Default store rate
        ]);

        $higherRates = $this->taxRuleFixtureFactory->createTaxRates([
            ['percentage' => 22, 'country' => 'US', 'region' => 42],
            ['percentage' => 10, 'country' => 'US', 'region' => 12], // Default store rate
        ]);

        $this->taxRules = $this->taxRuleFixtureFactory->createTaxRules([
            [
                'code' => 'Default Rule',
                'customer_tax_class_ids' => [$this->taxClasses['DefaultCustomerClass'], 3],
                'product_tax_class_ids' => [$this->taxClasses['DefaultProductClass']],
                'tax_rate_ids' => array_values($this->taxRates),
                'sort_order' => 0,
                'priority' => 0,
            ],
            [
                'code' => 'Higher Rate Rule',
                'customer_tax_class_ids' => [$this->taxClasses['DefaultCustomerClass'], 3],
                'product_tax_class_ids' => [$this->taxClasses['HigherProductClass']],
                'tax_rate_ids' => array_values($higherRates),
                'sort_order' => 0,
                'priority' => 0,
            ],
        ]);

        // For cleanup
        $this->taxRates = array_merge($this->taxRates, $higherRates);
    }

    /**
     * Get fixture customer address
     *
     * @return Address
     */
    private function getCustomerAddress()
    {
        $fixtureCustomerId = 1;
        $customerAddress = $this->objectManager->create(
            Address::class
        )->load($fixtureCustomerId);
        /** Set data which corresponds tax class fixture */
        $customerAddress->setCountryId('US')->setRegionId(42)->save();
        return $customerAddress;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getTaxPriceDataProvider()
    {
        return [
            'price is 0' => [
                (new DataObject())->setPrice(0),
                0,
            ],
            'no price conversion, round' => [
                (new DataObject())->setPrice(3.256)->setRoundPrice(true),
                '3.26',
            ],
            'no price conversion, no round' => [
                (new DataObject())->setPrice(3.256),
                '3.256',
            ],
            'price conversion, display including tax, round' => [
                (new DataObject())->setPrice(3.256)->setRoundPrice(true),
                '3.5',
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '0',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_INCLUDING_TAX,
                    ],
                ],
            ],
            'price conversion, display including tax, no round' => [
                (new DataObject())->setPrice(3.256)->setNotEqual(true),
                '3.5',  // should be not equal to rounded value (eg, 3.5045009999999999)
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '0',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_INCLUDING_TAX,
                    ],
                ],
            ],
            'price conversion, display including tax, high rate product tax class, cross boarder trade, round' => [
                (new DataObject())->setPrice(3.256)->setRoundPrice(true),
                '3.98', // rounding issue: old code expects 3.97
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '0',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_INCLUDING_TAX,
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED,
                        'value' => '1',
                    ],
                ],
                'HigherProductClass',
            ],
            'price include tax, display including tax, round' => [
                (new DataObject())->setPrice(3.256)->setRoundPrice(true),
                '3.26',
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '1',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_INCLUDING_TAX,
                    ],
                ],
            ],
            'price include tax, display excluding tax, round' => [
                (new DataObject())->setPrice(3.256)->setRoundPrice(true),
                '3.03',
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '1',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_EXCLUDING_TAX,
                    ],
                ],
            ],
            'price include tax, display excluding tax, request including tax, round' => [
                (new DataObject())->setPrice(3.256)
                    ->setRoundPrice(true)
                    ->setIncludingTax(true),
                '3.26',
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '1',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_EXCLUDING_TAX,
                    ],
                ],
            ],
            'price include tax, display excluding tax, high rate product tax class, round' => [
                (new DataObject())->setPrice(3.256)->setRoundPrice(true),
                '2.97',
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '1',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_EXCLUDING_TAX,
                    ],
                ],
                'HigherProductClass',
            ],
            'price include tax, display excluding tax, high rate product tax class, cross boarder trade, round' => [
                (new DataObject())->setPrice(3.256)->setRoundPrice(true),
                '2.67',
                [
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                        'value' => '1',
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                        'value' => Config::DISPLAY_TYPE_EXCLUDING_TAX,
                    ],
                    [
                        'path' => Config::CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED,
                        'value' => '1',
                    ],
                ],
                'HigherProductClass',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->helper = $this->objectManager->get(Data::class);
        $this->taxRuleFixtureFactory = new TaxRuleFixtureFactory();
        $this->scopeConfig = $this->objectManager->get(MutableScopeConfig::class);
    }

    protected function tearDown(): void
    {
        $this->tearDownDefaultRules();
    }

    /**
     * Helper function that tears down some default rules
     */
    private function tearDownDefaultRules()
    {
        if ($this->taxRules) {
            $this->taxRuleFixtureFactory->deleteTaxRules(array_values($this->taxRules));
        }
        if ($this->taxRates) {
            $this->taxRuleFixtureFactory->deleteTaxRates(array_values($this->taxRates));
        }
        if ($this->taxClasses) {
            $this->taxRuleFixtureFactory->deleteTaxClasses(array_values($this->taxClasses));
        }
    }
}

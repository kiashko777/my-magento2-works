<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Tax\Model\Sales\Total\Quote;

use LogicException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Group;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Item;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\ShippingAssignment;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;

/**
 * Test \Magento\Tax\Model\Sales\Total\Quote\Subtotal
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SubtotalTest extends TestCase
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public static function setUpBeforeClass(): void
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();

        parent::setUpBeforeClass();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoConfigFixture current_store tax/calculation/algorithm UNIT_BASE_CALCULATION
     * @dataProvider collectUnitBasedDataProvider
     * @param array $quoteItems
     * @param array $expected
     * @return void
     */
    public function testCollectUnitBased(array $quoteItems, array $expected): void
    {
        $this->quote($quoteItems, $expected);
    }

    /**
     * Create quote and assert totals values
     *
     * @param array $quoteItems
     * @param array $expected
     * @return void
     */
    private function quote(array $quoteItems, array $expected): void
    {
        $customerTaxClassId = $this->getCustomerTaxClassId();
        $fixtureCustomerId = 1;
        /** @var Customer $customer */
        $customer = $this->objectManager->create(Customer::class)->load($fixtureCustomerId);
        /** @var Group $customerGroup */
        $customerGroup = $this->objectManager->create(
            Group::class
        )->load(
            'custom_group',
            'customer_group_code'
        );
        $customerGroup->setTaxClassId($customerTaxClassId)->save();
        $customer->setGroupId($customerGroup->getId())->save();
        $productTaxClassId = $this->getProductTaxClassId();


        $quoteShippingAddressDataObject = $this->getShippingAddressDataObject($fixtureCustomerId);

        /** @var \Magento\Quote\Model\Quote\Address $quoteShippingAddress */
        $quoteShippingAddress = $this->objectManager->create(\Magento\Quote\Model\Quote\Address::class);
        $quoteShippingAddress->importCustomerAddressData($quoteShippingAddressDataObject);

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->setStoreId(
            1
        )->setIsActive(
            true
        )->setIsMultiShipping(
            false
        )->assignCustomerWithAddressChange(
            $this->getCustomerById($customer->getId())
        )->setShippingAddress(
            $quoteShippingAddress
        )->setBillingAddress(
            $quoteShippingAddress
        )->setCheckoutMethod(
            $customer->getMode()
        )->setPasswordHash(
            $customer->encryptPassword($customer->getPassword())
        );

        foreach ($quoteItems as $quoteItem) {
            $product = $quoteItem['product'] ?? null;
            if ($product === null) {
                /** @var Product $product */
                $product = $this->productRepository->get($quoteItem['sku'] ?? 'simple');
                $product->setTaxClassId($productTaxClassId)->save();
            }
            $quote->addProduct($product, $quoteItem['qty']);
        }

        $address = $quote->getShippingAddress();
        /** @var ShippingAssignment $shippingAssignment */
        $shippingAssignment = $this->objectManager->create(ShippingAssignment::class);
        $shipping = $this->objectManager->create(\Magento\Quote\Model\Shipping::class);
        $shipping->setAddress($address);
        $shippingAssignment->setShipping($shipping);
        $shippingAssignment->setItems($address->getAllItems());
        /** @var  Total $total */
        $total = $this->objectManager->create(Total::class);
        /** @var \Magento\Quote\Model\Quote\Address\Total\Subtotal $addressSubtotalCollector */
        $addressSubtotalCollector = $this->objectManager->create(
            \Magento\Quote\Model\Quote\Address\Total\Subtotal::class
        );
        $addressSubtotalCollector->collect($quote, $shippingAssignment, $total);

        /** @var Subtotal $subtotalCollector */
        $subtotalCollector = $this->objectManager->create(Subtotal::class);
        $subtotalCollector->collect($quote, $shippingAssignment, $total);

        $this->assertEquals($address->getSubtotal(), $total->getSubtotal());
        $this->assertEquals($address->getBaseSubtotal(), $total->getBaseSubtotal());
        $this->assertEquals($address->getBaseSubtotalTotalInclTax(), $total->getBaseSubtotalTotalInclTax());

        $this->assertEquals($expected[0], $total->toArray(array_keys($expected[0])));
        $actualAddressItemsData = [];
        if ($expected[1]) {
            $keys = array_keys($expected[1][0]);
            /** @var Item $addressItem */
            foreach ($address->getAllItems() as $addressItem) {
                $actualAddressItemsData[] = array_intersect_key($addressItem->toArray($keys), array_flip($keys));
            }
        }
        $this->assertEquals($expected[1], $actualAddressItemsData);
    }

    /**
     * Get customer tax class id
     *
     * @return int
     */
    protected function getCustomerTaxClassId()
    {
        $customerTaxClass = $this->objectManager->create(ClassModel::class);
        $fixtureCustomerTaxClass = 'CustomerTaxClass2';
        /** @var ClassModel $customerTaxClass */
        $customerTaxClass->load($fixtureCustomerTaxClass, 'class_name');
        return $customerTaxClass->getId();
    }

    /**
     * Get product tax class id
     *
     * @return int
     */
    protected function getProductTaxClassId()
    {
        /** @var ClassModel $productTaxClass */
        $productTaxClass = $this->objectManager->create(ClassModel::class);
        $fixtureProductTaxClass = 'ProductTaxClass1';
        $productTaxClass->load($fixtureProductTaxClass, 'class_name');
        return $productTaxClass->getId();
    }

    /**
     * @param $fixtureCustomerId
     * @return AddressInterface
     */
    protected function getShippingAddressDataObject($fixtureCustomerId)
    {
        $fixtureCustomerAddressId = 1;
        $customerAddress = $this->objectManager->create(
            Address::class
        )->load($fixtureCustomerId);
        /** Set data which corresponds tax class fixture */
        $customerAddress->setCountryId('US')->setRegionId(12)->save();
        /**
         * @var $addressRepository AddressRepositoryInterface
         */
        $addressRepository = $this->objectManager->get(AddressRepositoryInterface::class);
        $quoteShippingAddressDataObject = $addressRepository->getById($fixtureCustomerAddressId);
        return $quoteShippingAddressDataObject;
    }

    protected function getCustomerById($id)
    {
        /**
         * @var $customerRepository CustomerRepositoryInterface
         */
        $customerRepository = $this->objectManager->create(CustomerRepositoryInterface::class);
        return $customerRepository->getById($id);
    }

    public function collectUnitBasedDataProvider(): array
    {
        return [
            'one_item' => [
                [
                    [
                        'sku' => 'simple',
                        'qty' => 2
                    ],
                ],
                [
                    [
                        'subtotal' => 20,
                        'subtotal_incl_tax' => 21.5,
                        'base_subtotal_total_incl_tax' => 21.5,
                        'tax_amount' => 1.5,
                        'discount_amount' => 0,
                    ],
                    [
                        [
                            'tax_amount' => 1.5,
                            'price' => 10,
                            'price_incl_tax' => 10.75,
                            'row_total' => 20,
                            'row_total_incl_tax' => 21.5,
                            'tax_percent' => 7.5,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @magentoDataFixture Magento/Bundle/_files/product.php
     * @magentoConfigFixture current_store tax/calculation/algorithm UNIT_BASE_CALCULATION
     * @dataProvider collectUnitBasedBundleProductDataProvider
     * @param array $quoteItems
     * @param array $expected
     * @return void
     */
    public function testCollectUnitBasedBundleProduct(array $quoteItems, array $expected): void
    {
        $productTaxClassId = $this->getProductTaxClassId();
        /** @var Product $product */
        $childProduct = $this->productRepository->get('simple');
        $childProduct->setTaxClassId($productTaxClassId)->save();
        /** @var Product $product */
        $product = $this->productRepository->get('bundle-product');
        $product->setTaxClassId($productTaxClassId)
            ->setPriceType(AbstractType::CALCULATE_CHILD)
            ->save();
        $quoteItems[0]['product'] = $product;
        $this->quote($quoteItems, $expected);
    }

    public function collectUnitBasedBundleProductDataProvider(): array
    {
        return [
            'one_item' => [
                [
                    [
                        'sku' => 'bundle-product',
                        'qty' => 2
                    ],
                ],
                [
                    [
                        'subtotal' => 20,
                        'subtotal_incl_tax' => 21.5,
                        'base_subtotal_total_incl_tax' => 21.5,
                        'tax_amount' => 1.5,
                        'discount_amount' => 0,
                    ],
                    [
                        [
                            'tax_amount' => 1.5,
                            'price' => 10,
                            'price_incl_tax' => 10.75,
                            'row_total' => 20,
                            'row_total_incl_tax' => 21.5,
                            'tax_percent' => null,
                        ],
                        [
                            'tax_amount' => 1.5,
                            'price' => 10,
                            'price_incl_tax' => 10.75,
                            'row_total' => 20,
                            'row_total_incl_tax' => 21.5,
                            'tax_percent' => 7.5,
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_store tax/calculation/cross_border_trade_enabled 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoConfigFixture current_store tax/calculation/algorithm UNIT_BASE_CALCULATION
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @dataProvider collectUnitBasedPriceIncludesTaxDataProvider
     * @param array $quoteItems
     * @param array $expected
     */
    public function testCollectUnitBasedPriceIncludesTax(array $quoteItems, array $expected): void
    {
        $this->quote($quoteItems, $expected);
    }

    /**
     * @return array
     */
    public function collectUnitBasedPriceIncludesTaxDataProvider(): array
    {
        return [
            [
                [
                    [
                        'sku' => 'simple',
                        'qty' => 1
                    ],
                ],
                [
                    [
                        'subtotal' => 9.3,
                        'subtotal_incl_tax' => 10,
                        'base_subtotal_total_incl_tax' => 10,
                        'tax_amount' => 0.7,
                        'discount_amount' => 0,
                    ],
                    [
                        [
                            'tax_amount' => 0.7,
                            'price' => 9.3,
                            'price_incl_tax' => 10,
                            'row_total' => 9.3,
                            'row_total_incl_tax' => 10,
                            'tax_percent' => 7.5,
                        ],
                    ],
                ],
            ],
            [
                [
                    [
                        'sku' => 'simple',
                        'qty' => 2
                    ],
                ],
                [
                    [
                        'subtotal' => 18.6,
                        'subtotal_incl_tax' => 20,
                        'base_subtotal_total_incl_tax' => 20,
                        'tax_amount' => 1.4,
                        'discount_amount' => 0,
                    ],
                    [
                        [
                            'tax_amount' => 1.4,
                            'price' => 9.3,
                            'price_incl_tax' => 10,
                            'row_total' => 18.6,
                            'row_total_incl_tax' => 20,
                            'tax_percent' => 7.5,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
    }
}

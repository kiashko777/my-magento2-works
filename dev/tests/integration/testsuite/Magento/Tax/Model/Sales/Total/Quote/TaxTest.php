<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\Sales\Total\Quote;

use LogicException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Group;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Item;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;

require_once __DIR__ . '/SetupUtil.php';
require_once __DIR__ . '/../../../../_files/tax_calculation_data_aggregated.php';
require_once __DIR__ . '/../../../../_files/full_discount_with_tax.php';

/**
 * Class TaxTest
 *
 * Tests sales taxes with discounts/price rules during checkout.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxTest extends TestCase
{
    /**
     * Utility object for setting up tax rates, tax classes and tax rules
     *
     * @var SetupUtil
     */
    protected $setupUtil = null;

    /**
     * @var TotalsCollector
     */
    private $totalsCollector;

    /**
     * Test taxes collection for quote.
     *
     * Quote has customer and product.
     * Products tax class and customer group tax class along with billing address have corresponding tax rule.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCollect()
    {
        /** Preconditions */
        $objectManager = Bootstrap::getObjectManager();
        /** @var ClassModel $customerTaxClass */
        $customerTaxClass = $objectManager->create(ClassModel::class);
        $fixtureCustomerTaxClass = 'CustomerTaxClass2';
        $customerTaxClass->load($fixtureCustomerTaxClass, 'class_name');
        $fixtureCustomerId = 1;
        /** @var Customer $customer */
        $customer = $objectManager->create(Customer::class)->load($fixtureCustomerId);
        /** @var Group $customerGroup */
        $customerGroup = $objectManager->create(Group::class)
            ->load('custom_group', 'customer_group_code');
        $customerGroup->setTaxClassId($customerTaxClass->getId())->save();
        $customer->setGroupId($customerGroup->getId())->save();

        /** @var ClassModel $productTaxClass */
        $productTaxClass = $objectManager->create(ClassModel::class);
        $fixtureProductTaxClass = 'ProductTaxClass1';
        $productTaxClass->load($fixtureProductTaxClass, 'class_name');
        /** @var Product $product */
        $product = $objectManager->create(ProductRepositoryInterface::class)->get('simple');
        $product->setTaxClassId($productTaxClass->getId())->save();

        $fixtureCustomerAddressId = 1;
        $customerAddress = $objectManager->create(\Magento\Customer\Model\Address::class)->load($fixtureCustomerId);
        /** Set data which corresponds tax class fixture */
        $customerAddress->setCountryId('US')->setRegionId(12)->save();
        /** @var Address $quoteShippingAddress */
        $quoteShippingAddress = $objectManager->create(Address::class);
        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $objectManager->create(AddressRepositoryInterface::class);
        $quoteShippingAddress->importCustomerAddressData($addressRepository->getById($fixtureCustomerAddressId));

        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $objectManager->create(CustomerRepositoryInterface::class);
        /** @var Quote $quote */
        $quote = $objectManager->create(Quote::class);
        $quote->setStoreId(1)
            ->setIsActive(true)
            ->setIsMultiShipping(false)
            ->assignCustomerWithAddressChange($customerRepository->getById($customer->getId()))
            ->setShippingAddress($quoteShippingAddress)
            ->setBillingAddress($quoteShippingAddress)
            ->setCheckoutMethod($customer->getMode())
            ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
            ->addProduct($product->load($product->getId()), 2);

        /**
         * Execute SUT.
         * \Magento\Tax\Model\Sales\Total\Quote\Tax::collect cannot be called separately from
         * \Magento\Tax\Model\Sales\Total\Quote\Subtotal::collect because tax to zero amount will be applied.
         * That is why it make sense to call collectTotals() instead, which will call SUT in its turn.
         */
        $quote->collectTotals();

        /** Check results */
        $this->assertEquals(
            $customerTaxClass->getId(),
            $quote->getCustomerTaxClassId(),
            'Customer tax class ID in quote is invalid.'
        );
        $this->assertEquals(
            21.5,
            $quote->getGrandTotal(),
            'Customer tax was collected by \Magento\Tax\Model\Sales\Total\Quote\Tax::collect incorrectly.'
        );
    }

    /**
     * Test taxes collection with full discount for quote.
     *
     * Test tax calculation and price when the discount may be bigger than total
     * This method will test the collector through $quote->collectTotals() method
     *
     * @see \Magento\SalesRule\Model\Utility::deltaRoundingFix
     * @magentoDataFixture Magento/Tax/_files/full_discount_with_tax.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullDiscountWithDeltaRoundingFix()
    {
        global $fullDiscountIncTax;
        $configData = $fullDiscountIncTax['config_data'];
        $quoteData = $fullDiscountIncTax['quote_data'];
        $expectedResults = $fullDiscountIncTax['expected_result'];

        /** @var  ObjectManagerInterface $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        //Setup tax configurations
        $this->setupUtil = new SetupUtil($objectManager);
        $this->setupUtil->setupTax($configData);

        $quote = $this->setupUtil->setupQuote($quoteData);

        $quote->collectTotals();

        $quoteAddress = $quote->getShippingAddress();

        $this->verifyResult($quoteAddress, $expectedResults);
    }

    /**
     * Verify fields in quote address and quote item are correct
     *
     * @param Address $quoteAddress
     * @param array $expectedResults
     * @return $this
     */
    protected function verifyResult($quoteAddress, $expectedResults)
    {
        $addressData = $expectedResults['address_data'];

        $this->verifyQuoteAddress($quoteAddress, $addressData);

        $quoteItems = $quoteAddress->getAllItems();
        foreach ($quoteItems as $item) {
            /** @var  Item $item */
            $sku = $item->getProduct()->getSku();
            $expectedItemData = $expectedResults['items_data'][$sku];
            $this->verifyItem($item, $expectedItemData);
        }

        return $this;
    }

    /**
     * Verify fields in quote address
     *
     * @param Address $quoteAddress
     * @param array $expectedAddressData
     * @return $this
     */
    protected function verifyQuoteAddress($quoteAddress, $expectedAddressData)
    {
        foreach ($expectedAddressData as $key => $value) {
            if ($key == 'applied_taxes') {
                $this->verifyAppliedTaxes($quoteAddress->getAppliedTaxes(), $value);
            } else {
                $this->assertEquals($value, $quoteAddress->getData($key), 'Quote address ' . $key . ' is incorrect');
            }
        }

        return $this;
    }

    /**
     * Verify that applied taxes are correct
     *
     * @param array $appliedTaxes
     * @param array $expectedAppliedTaxes
     * @return $this
     */
    protected function verifyAppliedTaxes($appliedTaxes, $expectedAppliedTaxes)
    {
        foreach ($expectedAppliedTaxes as $taxRateKey => $expectedTaxRate) {
            $this->assertTrue(isset($appliedTaxes[$taxRateKey]), 'Missing tax rate ' . $taxRateKey);
            $this->verifyAppliedTax($appliedTaxes[$taxRateKey], $expectedTaxRate);
        }
        return $this;
    }

    /**
     * Verify one row in the applied taxes
     *
     * @param array $appliedTax
     * @param array $expectedAppliedTax
     * @return $this
     */
    protected function verifyAppliedTax($appliedTax, $expectedAppliedTax)
    {
        foreach ($expectedAppliedTax as $key => $value) {
            if ($key == 'rates') {
                foreach ($value as $index => $taxRate) {
                    $this->verifyAppliedTaxRate($appliedTax['rates'][$index], $taxRate);
                }
            } else {
                $this->assertEquals($value, $appliedTax[$key], 'Applied tax ' . $key . ' is incorrect');
            }
        }
        return $this;
    }

    /**
     * Verify one tax rate in a tax row
     *
     * @param array $appliedTaxRate
     * @param array $expectedAppliedTaxRate
     * @return $this
     */
    protected function verifyAppliedTaxRate($appliedTaxRate, $expectedAppliedTaxRate)
    {
        foreach ($expectedAppliedTaxRate as $key => $value) {
            $this->assertEquals($value, $appliedTaxRate[$key], 'Applied tax rate ' . $key . ' is incorrect');
        }
        return $this;
    }

    /**
     * Verify fields in quote item
     *
     * @param Item $item
     * @param array $expectedItemData
     * @return $this
     */
    protected function verifyItem($item, $expectedItemData)
    {
        foreach ($expectedItemData as $key => $value) {
            $this->assertEquals($value, $item->getData($key), 'item ' . $key . ' is incorrect');
        }

        return $this;
    }

    /**
     * Test tax calculation with various configuration and combination of items
     * This method will test various collectors through $quoteAddress->collectTotals() method
     *
     * @param array $configData
     * @param array $quoteData
     * @param array $expectedResults
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @dataProvider taxDataProvider
     * @return void
     */
    public function testTaxCalculation($configData, $quoteData, $expectedResults)
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();
        //Setup tax configurations
        $this->setupUtil->setupTax($configData);

        $quote = $this->setupUtil->setupQuote($quoteData);
        $quoteAddress = $quote->getShippingAddress();
        $this->totalsCollector->collectAddressTotals($quote, $quoteAddress);
        $this->verifyResult($quoteAddress, $expectedResults);

        $skus = array_map(function ($item) {
            return $item['sku'];
        }, $quoteData['items'] ?? []);
        $this->removeProducts($skus);
    }

    /**
     * Cleanup test by removing products.
     *
     * @param string[] $skus
     * @return void
     */
    private function removeProducts(array $skus): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $objectManager->create(ProductRepositoryInterface::class);
        $registry = $objectManager->get(Registry::class);
        /** @var ProductRepositoryInterface $productRepository */
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        foreach ($skus as $sku) {
            try {
                $productRepository->deleteById($sku);
            } catch (NoSuchEntityException $e) {
                // product already deleted
            }
        }

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }

    /**
     * Read the array defined in ../../../../_files/tax_calculation_data_aggregated.php
     * and feed it to testTaxCalculation
     *
     * @return array
     */
    public function taxDataProvider()
    {
        global $taxCalculationData;
        return $taxCalculationData;
    }

    /**
     * test setup
     */
    protected function setUp(): void
    {
        /** @var  ObjectManagerInterface $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $this->totalsCollector = $objectManager->create(TotalsCollector::class);
        $this->setupUtil = new SetupUtil($objectManager);

        parent::setUp();
    }
}

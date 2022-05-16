<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Weee\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Group;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDataFixture Magento/Customer/_files/customer_sample.php
 * @magentoDataFixture Magento/Catalog/_files/products.php
 * @magentoDataFixture Magento/Weee/_files/product_with_fpt.php
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxTest extends TestCase
{
    /**
     * @var Tax
     */
    protected $_model;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $_extensibleDataObjectConverter;

    public function testGetProductWeeeAttributes()
    {
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = Bootstrap::getObjectManager()->create(
            CustomerRepositoryInterface::class
        );
        $customerMetadataService = Bootstrap::getObjectManager()->create(
            CustomerMetadataInterface::class
        );
        $customerFactory = Bootstrap::getObjectManager()->create(
            CustomerInterfaceFactory::class,
            ['metadataService' => $customerMetadataService]
        );
        $dataObjectHelper = Bootstrap::getObjectManager()->create(DataObjectHelper::class);
        $expected = $this->_extensibleDataObjectConverter->toFlatArray(
            $customerRepository->getById(1),
            [],
            CustomerInterface::class
        );
        $customerDataSet = $customerFactory->create();
        $dataObjectHelper->populateWithArray(
            $customerDataSet,
            $expected,
            CustomerInterface::class
        );
        $fixtureGroupCode = 'custom_group';
        $fixtureTaxClassId = 3;
        /** @var Group $group */
        $group = Bootstrap::getObjectManager()->create(Group::class);
        $fixtureGroupId = $group->load($fixtureGroupCode, 'customer_group_code')->getId();
        /** @var Quote $quote */
        $quote = Bootstrap::getObjectManager()->create(Quote::class);
        $quote->setCustomerGroupId($fixtureGroupId);
        $quote->setCustomerTaxClassId($fixtureTaxClassId);
        $quote->setCustomer($customerDataSet);
        $shipping = new DataObject([
            'quote' => $quote,
        ]);
        $productRepository = Bootstrap::getObjectManager()->create(
            ProductRepositoryInterface::class
        );
        $product = $productRepository->get('simple-with-ftp');

        $amount = $this->_model->getProductWeeeAttributes($product, $shipping, null, null, true);
        $this->assertIsArray($amount);
        $this->assertArrayHasKey(0, $amount);
        $this->assertEquals(12.70, $amount[0]->getAmount());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $weeeConfig = $this->createMock(Config::class);
        $weeeConfig->expects($this->any())->method('isEnabled')->willReturn(true);
        $weeeConfig->expects($this->any())->method('isTaxable')->willReturn(true);
        $attribute = $this->createMock(Attribute::class);
        $attribute->expects($this->any())->method('getAttributeCodesByFrontendType')->willReturn(
            ['weee']
        );
        $attributeFactory = $this->createPartialMock(AttributeFactory::class, ['create']);
        $attributeFactory->expects($this->any())->method('create')->willReturn($attribute);
        $this->_model = $objectManager->create(
            Tax::class,
            ['weeeConfig' => $weeeConfig, 'attributeFactory' => $attributeFactory]
        );
        $this->_extensibleDataObjectConverter = $objectManager->get(
            ExtensibleDataObjectConverter::class
        );
    }
}

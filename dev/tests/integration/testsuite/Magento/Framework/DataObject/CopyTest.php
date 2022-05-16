<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\DataObject;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Copy\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleExtensionAttributes\Api\Data\CustomerInterface;
use Magento\TestModuleExtensionAttributes\Api\Data\FakeCustomerInterface;
use Magento\TestModuleExtensionAttributes\Model\Data\FakeAttributeMetadata;
use Magento\TestModuleExtensionAttributes\Model\Data\FakeCustomerFactory;
use PHPUnit\Framework\TestCase;

class CopyTest extends TestCase
{
    /**
     * @var Copy
     */
    protected $_service;

    public function testCopyFieldset()
    {
        $fieldset = 'sales_copy_order';
        $aspect = 'to_edit';
        $data = ['customer_email' => 'admin@example.com', 'customer_group_id' => '1'];
        $source = new DataObject($data);
        $target = new DataObject();
        $expectedTarget = new DataObject($data);

        $this->assertNull($this->_service->copyFieldsetToTarget($fieldset, $aspect, 'invalid_source', []));
        $this->assertNull($this->_service->copyFieldsetToTarget($fieldset, $aspect, [], 'invalid_target'));
        $this->assertEquals(
            $target,
            $this->_service->copyFieldsetToTarget('invalid_fieldset', $aspect, $source, $target)
        );
        $this->assertSame($target, $this->_service->copyFieldsetToTarget($fieldset, $aspect, $source, $target));
        $this->assertEquals($expectedTarget, $target);
    }

    public function testCopyFieldsetWithExtensionAttributes()
    {
        $objectManager = Bootstrap::getObjectManager();

        $fieldsetConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldSet'])
            ->getMock();

        $service = $objectManager->create(
            Copy::class,
            ['fieldsetConfig' => $fieldsetConfigMock]
        );

        $data = ['firstname' => ['name' => '*'], 'lastname' => ['name' => '*'], 'test_group_code' => ['name' => '*']];
        $fieldsetConfigMock
            ->expects($this->once())
            ->method('getFieldSet')
            ->willReturn($data);

        $fieldset = 'customer_account';
        $aspect = 'name';
        $groupCode = 'general';
        $firstName = 'First';
        $data = [
            'email' => 'customer@example.com',
            'firstname' => $firstName,
            'lastname' => 'Last',
            // see declaration in dev/tests/integration/testsuite/Magento/Framework/Api/etc/extension_attributes.xml
            'extension_attributes' => ['test_group_code' => $groupCode]
        ];
        $dataWithExtraField = array_merge($data, ['undeclared_field' => 'will be omitted']);

        /** @var DataObjectHelper $dataObjectHelper */
        $dataObjectHelper = $objectManager->get(DataObjectHelper::class);
        /** @var FakeCustomerFactory $customerFactory */
        $customerFactory = $objectManager->get(
            FakeCustomerFactory::class
        );
        /** @var CustomerInterface $source */
        $source = $customerFactory->create();
        $dataObjectHelper->populateWithArray(
            $source,
            $dataWithExtraField,
            FakeCustomerInterface::class
        );
        /** @var CustomerInterface $target */
        $target = $customerFactory->create();
        $target = $service->copyFieldsetToTarget($fieldset, $aspect, $source, $target);

        $this->assertInstanceOf(FakeCustomerInterface::class, $target);
        $this->assertNull(
            $target->getEmail(),
            "Email should not be set because it is not defined in the fieldset."
        );
        $this->assertEquals(
            $firstName,
            $target->getFirstname(),
            "First name was not copied."
        );
        $this->assertEquals(
            $groupCode,
            $target->getExtensionAttributes()->getTestGroupCode(),
            "Extension attribute was not copied."
        );
    }

    public function testCopyFieldsetWithAbstractSimpleObject()
    {
        $objectManager = Bootstrap::getObjectManager();

        $fieldset = 'sales_copy_order';
        $aspect = 'to_edit';

        $fieldsetConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldSet'])
            ->getMock();

        $service = $objectManager->create(
            Copy::class,
            ['fieldsetConfig' => $fieldsetConfigMock]
        );

        $data = ['store_label' => ['to_edit' => '*'], 'frontend_label' => ['to_edit' => '*'],
            'attribute_code' => ['to_edit' => '*'], 'note' => ['to_edit' => '*']];
        $fieldsetConfigMock
            ->expects($this->any())
            ->method('getFieldSet')
            ->willReturn($data);

        $source = $objectManager->get(FakeAttributeMetadata::class);
        $source->setStoreLabel('storeLabel');
        $source->setFrontendLabel('frontendLabel');
        $source->setAttributeCode('attributeCode');
        $source->setNote('note');

        $target = $objectManager->get(FakeAttributeMetadata::class);
        $expectedTarget = $source;

        $this->assertEquals(
            $target,
            $service->copyFieldsetToTarget('invalid_fieldset', $aspect, $source, $target)
        );
        $this->assertEquals(
            $expectedTarget,
            $service->copyFieldsetToTarget($fieldset, $aspect, $source, $target)
        );
    }

    public function testCopyFieldsetArrayTarget()
    {
        $fieldset = 'sales_copy_order';
        $aspect = 'to_edit';
        $data = ['customer_email' => 'admin@example.com', 'customer_group_id' => '1'];
        $source = new DataObject($data);
        $target = [];
        $expectedTarget = $data;

        $this->assertEquals(
            $target,
            $this->_service->copyFieldsetToTarget('invalid_fieldset', $aspect, $source, $target)
        );
        $this->assertEquals(
            $expectedTarget,
            $this->_service->copyFieldsetToTarget($fieldset, $aspect, $source, $target)
        );
    }

    protected function setUp(): void
    {
        $this->_service = Bootstrap::getObjectManager()
            ->get(Copy::class);
    }
}

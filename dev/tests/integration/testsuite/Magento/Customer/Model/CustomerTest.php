<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    /**
     * @var Customer
     */
    protected $customerModel;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerFactory;

    public function testUpdateDataSetDataOnEmptyModel()
    {
        /** @var \Magento\Customer\Model\Data\Customer $customerData */
        $customerData = $this->customerFactory->create()
            ->setId(1)
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setDefaultBilling(1);
        $customerData = $this->customerModel->updateData($customerData)->getDataModel();

        $this->assertEquals(1, $customerData->getId());
        $this->assertEquals('John', $customerData->getFirstname());
        $this->assertEquals('Doe', $customerData->getLastname());
        $this->assertEquals(1, $customerData->getDefaultBilling());
    }

    public function testUpdateDataOverrideExistingData()
    {
        /** @var \Magento\Customer\Model\Data\Customer $customerData */
        $customerData = $this->customerFactory->create()
            ->setId(2)
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setDefaultBilling(1);
        $this->customerModel->updateData($customerData);

        /** @var \Magento\Customer\Model\Data\Customer $updatedCustomerData */
        $updatedCustomerData = $this->customerFactory->create()
            ->setId(3)
            ->setFirstname('Jane')
            ->setLastname('Smith')
            ->setDefaultBilling(0);
        $updatedCustomerData = $this->customerModel->updateData($updatedCustomerData)->getDataModel();

        $this->assertEquals(3, $updatedCustomerData->getId());
        $this->assertEquals('Jane', $updatedCustomerData->getFirstname());
        $this->assertEquals('Smith', $updatedCustomerData->getLastname());
        $this->assertEquals(0, $updatedCustomerData->getDefaultBilling());
    }

    protected function setUp(): void
    {
        $this->customerModel = Bootstrap::getObjectManager()->create(
            Customer::class
        );
        $this->customerFactory = Bootstrap::getObjectManager()->create(
            CustomerInterfaceFactory::class
        );
    }
}

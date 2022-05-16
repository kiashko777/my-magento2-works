<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CalculationTest
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoDataFixture Magento/Customer/_files/customer_address.php
 */
class CalculationTest extends TestCase
{
    const FIXTURE_CUSTOMER_ID = 1;
    const FIXTURE_ADDRESS_ID = 1;
    /**
     * @var ObjectManager
     */
    protected $_objectManager;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var Calculation
     */
    protected $_model;

    public function testDefaultCustomerTaxClass()
    {
        $defaultCustomerTaxClass = 3;
        $this->assertEquals($defaultCustomerTaxClass, $this->_model->getDefaultCustomerTaxClass(null));
    }

    public function testGetDefaultRateRequest()
    {
        $customerDataSet = $this->customerRepository->getById(self::FIXTURE_CUSTOMER_ID);
        $address = $this->addressRepository->getById(self::FIXTURE_ADDRESS_ID);

        $rateRequest = $this->_model->getRateRequest(null, null, null, null, $customerDataSet->getId());

        $this->assertNotNull($rateRequest);
        $this->assertEquals($address->getCountryId(), $rateRequest->getCountryId());
        $this->assertEquals($address->getRegion()->getRegionId(), $rateRequest->getRegionId());
        $this->assertEquals($address->getPostcode(), $rateRequest->getPostcode());

        $customerTaxClassId = $this->groupRepository->getById($customerDataSet->getGroupId())->getTaxClassId();
        $this->assertEquals($customerTaxClassId, $rateRequest->getCustomerClassId());
    }

    protected function setUp(): void
    {
        /** @var $objectManager ObjectManager */
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create(Calculation::class);
        $this->customerRepository = $this->_objectManager->create(
            CustomerRepositoryInterface::class
        );
        $this->addressRepository = $this->_objectManager->create(
            AddressRepositoryInterface::class
        );
        $this->groupRepository = $this->_objectManager->create(GroupRepositoryInterface::class);
    }
}

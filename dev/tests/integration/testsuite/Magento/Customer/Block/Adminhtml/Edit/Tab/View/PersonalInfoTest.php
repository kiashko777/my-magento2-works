<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use IntlDateFormatter;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Magento\Customer\Block\Adminhtml\Edit\Tab\View
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @magentoAppArea Adminhtml
 */
class PersonalInfoTest extends TestCase
{
    /**
     * @var DateTime
     */
    protected $dateTime;
    /** @var  Context */
    private $_context;
    /** @var  Registry */
    private $_coreRegistry;
    /** @var  CustomerInterfaceFactory */
    private $_customerFactory;
    /** @var  CustomerRepositoryInterface */
    private $_customerRepository;
    /** @var  GroupRepositoryInterface */
    private $_groupRepository;
    /** @var StoreManagerInterface */
    private $_storeManager;
    /** @var ObjectManagerInterface */
    private $_objectManager;
    /** @var DataObjectProcessor */
    private $_dataObjectProcessor;
    /** @var  PersonalInfo */
    private $_block;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomer()
    {
        $expectedCustomer = $this->_loadCustomer();
        $expectedCustomerData = $this->_dataObjectProcessor->buildOutputDataArray(
            $expectedCustomer,
            CustomerInterface::class
        );
        $actualCustomer = $this->_block->getCustomer();
        $actualCustomerData = $this->_dataObjectProcessor->buildOutputDataArray(
            $actualCustomer,
            CustomerInterface::class
        );
        foreach ($expectedCustomerData as $property => $value) {
            $expectedValue = is_numeric($value) ? (int)$value : $value;
            $actualValue = isset($actualCustomerData[$property]) ? $actualCustomerData[$property] : null;
            $actualValue = is_numeric($actualValue) ? (int)$actualValue : $actualValue;
            $this->assertEquals($expectedValue, $actualValue);
        }
    }

    /**
     * @return CustomerInterface
     */
    private function _loadCustomer()
    {
        $customer = $this->_customerRepository->getById(1);
        $data = ['account' => $this->_dataObjectProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class),];
        $this->_context->getBackendSession()->setCustomerData($data);
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());
        return $customer;
    }

    public function testGetCustomerEmpty()
    {
        $expectedCustomer = $this->createCustomerAndAddToBackendSession();
        $actualCustomer = $this->_block->getCustomer();
        $this->assertEquals($expectedCustomer->getExtensionAttributes(), $actualCustomer->getExtensionAttributes());
        $this->assertEquals($expectedCustomer, $actualCustomer);
    }

    /**
     * @return CustomerInterface
     */
    private function createCustomerAndAddToBackendSession()
    {
        /** @var CustomerInterface $customer */
        $customer = $this->_customerFactory->create()->setFirstname(
            'firstname'
        )->setLastname(
            'lastname'
        )->setEmail(
            'email@email.com'
        );
        $data = ['account' => $this->_dataObjectProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class),];
        $this->_context->getBackendSession()->setCustomerData($data);
        return $customer;
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetGroupName()
    {
        $groupName = $this->_groupRepository->getById($this->_loadCustomer()->getGroupId())->getCode();
        $this->assertEquals($groupName, $this->_block->getGroupName());
    }

    public function testGetGroupNameNull()
    {
        $this->createCustomerAndAddToBackendSession();
        $this->assertNull($this->_block->getGroupName());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCreateDate()
    {
        $createdAt = $this->_block->formatDate(
            $this->_loadCustomer()->getCreatedAt(),
            IntlDateFormatter::MEDIUM,
            true
        );
        $this->assertEquals($createdAt, $this->_block->getCreateDate());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetStoreCreateDate()
    {
        $customer = $this->_loadCustomer();
        $localeDate = $this->_context->getLocaleDate();
        $timezone = $localeDate->getConfigTimezone(
            ScopeInterface::SCOPE_STORE,
            $customer->getStoreId()
        );
        $storeCreateDate = $this->_block->formatDate(
            $customer->getCreatedAt(),
            IntlDateFormatter::MEDIUM,
            true,
            null,
            $timezone
        );
        $this->assertEquals($storeCreateDate, $this->_block->getStoreCreateDate());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetStoreCreateDateTimezone()
    {
        /**
         * @var TimezoneInterface $defaultTimeZonePath
         */
        $defaultTimeZonePath = $this->_objectManager->get(TimezoneInterface::class)
            ->getDefaultTimezonePath();
        $timezone = $this->_context->getScopeConfig()->getValue(
            $defaultTimeZonePath,
            ScopeInterface::SCOPE_STORE,
            $this->_loadCustomer()->getStoreId()
        );
        $this->assertEquals($timezone, $this->_block->getStoreCreateDateTimezone());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsConfirmedStatusConfirmed()
    {
        $this->_loadCustomer();
        $this->assertEquals(__('Confirmation Not Required'), $this->_block->getIsConfirmedStatus());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testIsConfirmedStatusConfirmationIsNotRequired()
    {
        $password = 'password';
        /** @var CustomerInterface $customer */
        $customer = $this->_customerFactory->create()->setConfirmation(
            true
        )->setFirstname(
            'firstname'
        )->setLastname(
            'lastname'
        )->setEmail(
            'email@email.com'
        );
        $customer = $this->_customerRepository->save($customer, $password);
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());
        $this->assertEquals('Confirmation Not Required', $this->_block->getIsConfirmedStatus());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCreatedInStore()
    {
        $storeName = $this->_storeManager->getStore($this->_loadCustomer()->getStoreId())->getName();
        $this->assertEquals($storeName, $this->_block->getCreatedInStore());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetBillingAddressHtml()
    {
        $this->_loadCustomer();
        $html = $this->_block->getBillingAddressHtml();
        $this->assertStringContainsString('John Smith<br />', $html);
        $this->assertStringContainsString('Green str, 67<br />', $html);
        $this->assertStringContainsString('CityM,  Alabama, 75477<br />', $html);
    }

    public function testGetBillingAddressHtmlNoDefaultAddress()
    {
        $this->createCustomerAndAddToBackendSession();
        $this->assertEquals(
            __('The customer does not have default billing address.'),
            $this->_block->getBillingAddressHtml()
        );
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();

        $this->_storeManager = $this->_objectManager->get(StoreManagerInterface::class);
        $this->_context = $this->_objectManager->get(
            Context::class,
            ['storeManager' => $this->_storeManager]
        );

        $this->_customerFactory = $this->_objectManager->get(
            CustomerInterfaceFactory::class
        );
        $this->_coreRegistry = $this->_objectManager->get(Registry::class);
        $this->_customerRepository = $this->_objectManager->get(
            CustomerRepositoryInterface::class
        );
        $this->_dataObjectProcessor = $this->_objectManager->get(
            DataObjectProcessor::class
        );

        $this->_groupRepository = $this->_objectManager->get(GroupRepositoryInterface::class);
        $this->dateTime = $this->_objectManager->get(DateTime::class);

        $this->_block = $this->_objectManager->get(
            LayoutInterface::class
        )->createBlock(
            PersonalInfo::class,
            '',
            [
                'context' => $this->_context,
                'groupService' => $this->_groupRepository,
                'registry' => $this->_coreRegistry
            ]
        );
    }

    protected function tearDown(): void
    {
        $this->_coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
        /** @var CustomerRegistry $customerRegistry */
        $customerRegistry = $this->_objectManager->get(CustomerRegistry::class);
        //Cleanup customer from registry
        $customerRegistry->remove(1);
    }
}

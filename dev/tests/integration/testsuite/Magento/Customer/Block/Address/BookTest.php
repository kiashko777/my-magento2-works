<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    /**
     * @var Book
     */
    protected $_block;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    public function testGetAddressEditUrl()
    {
        $this->assertEquals(
            'http://localhost/index.php/customer/address/edit/id/1/',
            $this->_block->getAddressEditUrl(1)
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider hasPrimaryAddressDataProvider
     * @magentoAppIsolation enabled
     */
    public function testHasPrimaryAddress($customerId, $expected)
    {
        if (!empty($customerId)) {
            $this->currentCustomer->setCustomerId($customerId);
        }
        $this->assertEquals($expected, $this->_block->hasPrimaryAddress());
    }

    public function hasPrimaryAddressDataProvider()
    {
        return ['0' => [0, false], '1' => [1, true], '5' => [5, false]];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testGetAdditionalAddresses()
    {
        $this->currentCustomer->setCustomerId(1);
        $this->assertNotNull($this->_block->getAdditionalAddresses());
        $this->assertCount(1, $this->_block->getAdditionalAddresses());
        $this->assertInstanceOf(
            AddressInterface::class,
            $this->_block->getAdditionalAddresses()[0]
        );
        $this->assertEquals(2, $this->_block->getAdditionalAddresses()[0]->getId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getAdditionalAddressesDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetAdditionalAddressesNegative($customerId, $expected)
    {
        if (!empty($customerId)) {
            $this->currentCustomer->setCustomerId($customerId);
        }
        $this->assertEquals($expected, $this->_block->getAdditionalAddresses());
    }

    public function getAdditionalAddressesDataProvider()
    {
        return ['0' => [0, false], '5' => [5, false]];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testGetAddressHtml()
    {
        $expected = "John Smith<br />\nCompanyName<br />\nGreen str, 67<br />\n\n\n\nCityM,  Alabama, 75477<br />" .
            "\nUnited States<br />\nT: <a href=\"tel:3468676\">3468676</a>\n\n";
        $address = Bootstrap::getObjectManager()->get(
            AddressRepositoryInterface::class
        )->getById(1);
        $html = $this->_block->getAddressHtml($address);
        $this->assertEquals($expected, $html);
    }

    public function testGetAddressHtmlWithoutAddress()
    {
        $this->assertEquals('', $this->_block->getAddressHtml(null));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testGetCustomer()
    {
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = Bootstrap::getObjectManager()->get(
            CustomerRepositoryInterface::class
        );
        $customer = $customerRepository->getById(1);

        $this->currentCustomer->setCustomerId(1);
        $object = $this->_block->getCustomer();
        $this->assertEquals($customer, $object);
    }

    public function testGetCustomerMissingCustomer()
    {
        $this->assertNull($this->_block->getCustomer());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getDefaultBillingDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetDefaultBilling($customerId, $expected)
    {
        $this->currentCustomer->setCustomerId($customerId);
        $this->assertEquals($expected, $this->_block->getDefaultBilling());
    }

    public function getDefaultBillingDataProvider()
    {
        return ['0' => [0, null], '1' => [1, 1], '5' => [5, null]];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getDefaultShippingDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetDefaultShipping($customerId, $expected)
    {
        if (!empty($customerId)) {
            $this->currentCustomer->setCustomerId($customerId);
        }
        $this->assertEquals($expected, $this->_block->getDefaultShipping());
    }

    public function getDefaultShippingDataProvider()
    {
        return ['0' => [0, null], '1' => [1, 1], '5' => [5, null]];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetAddressById()
    {
        $this->assertInstanceOf(AddressInterface::class, $this->_block->getAddressById(1));
        $this->assertNull($this->_block->getAddressById(5));
    }

    protected function setUp(): void
    {
        /** @var MockObject $blockMock */
        $blockMock = $this->getMockBuilder(
            BlockInterface::class
        )->disableOriginalConstructor()->setMethods(
            ['setTitle', 'toHtml']
        )->getMock();
        $blockMock->expects($this->any())->method('setTitle');

        $this->currentCustomer = Bootstrap::getObjectManager()
            ->get(CurrentCustomer::class);
        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $layout->setBlock('head', $blockMock);
        $this->_block = $layout
            ->createBlock(
                Book::class,
                '',
                ['currentCustomer' => $this->currentCustomer]
            );
    }

    protected function tearDown(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var CustomerRegistry $customerRegistry */
        $customerRegistry = $objectManager->get(CustomerRegistry::class);
        // Cleanup customer from registry
        $customerRegistry->remove(1);
    }
}

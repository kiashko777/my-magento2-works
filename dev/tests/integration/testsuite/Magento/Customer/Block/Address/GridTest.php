<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Address;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for the \Magento\Customer\Block\Address\Grid class
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GridTest extends TestCase
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testGetAddressEditUrl()
    {
        $gridBlock = $this->createBlockForCustomer(1);

        $this->assertEquals(
            'http://localhost/index.php/customer/address/edit/id/1/',
            $gridBlock->getAddressEditUrl(1)
        );
    }

    /**
     * Create address book block for customer
     *
     * @param int $customerId
     * @return BlockInterface
     */
    private function createBlockForCustomer($customerId)
    {
        $this->currentCustomer->setCustomerId($customerId);
        return $this->layout->createBlock(
            Grid::class,
            '',
            ['currentCustomer' => $this->currentCustomer]
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testGetAdditionalAddresses()
    {
        $gridBlock = $this->createBlockForCustomer(1);
        $this->assertNotNull($gridBlock->getAdditionalAddresses());
        $this->assertCount(1, $gridBlock->getAdditionalAddresses());
        $this->assertInstanceOf(
            AddressInterface::class,
            $gridBlock->getAdditionalAddresses()[0]
        );
        $this->assertEquals(2, $gridBlock->getAdditionalAddresses()[0]->getId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getAdditionalAddressesDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetAdditionalAddressesNegative($customerId, $expected)
    {
        $gridBlock = $this->createBlockForCustomer($customerId);
        $this->currentCustomer->setCustomerId($customerId);
        $this->assertEquals($expected, $gridBlock->getAdditionalAddresses());
    }

    public function getAdditionalAddressesDataProvider()
    {
        return ['5' => [5, []]];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @magentoAppIsolation enabled
     */
    public function testGetAddressHtmlWithoutAddress()
    {
        $gridBlock = $this->createBlockForCustomer(5);
        $this->assertEquals('', $gridBlock->getAddressHtml(null));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testGetCustomer()
    {
        $gridBlock = $this->createBlockForCustomer(1);
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = Bootstrap::getObjectManager()->get(
            CustomerRepositoryInterface::class
        );
        $customer = $customerRepository->getById(1);
        $object = $gridBlock->getCustomer();
        $this->assertEquals($customer, $object);
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
        $this->layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $this->layout->setBlock('head', $blockMock);
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

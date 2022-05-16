<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Multishipping\Block\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class AddressesTest extends TestCase
{
    const FIXTURE_CUSTOMER_ID = 1;

    /**
     * @var Addresses
     */
    protected $_addresses;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Checkout/_files/quote_with_product_and_payment.php
     */
    public function testGetAddressOptions()
    {
        $expectedResult = [
            [
                'value' => '1',
                'label' => 'John Smith, Green str, 67, CityM, Alabama 75477, United States',
            ],
        ];

        $addressAsHtml = $this->_addresses->getAddressOptions();
        $this->assertEquals($expectedResult, $addressAsHtml);
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = Bootstrap::getObjectManager()->create(
            CustomerRepositoryInterface::class
        );
        $customerData = $customerRepository->getById(self::FIXTURE_CUSTOMER_ID);

        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
        $customerSession->setCustomerData($customerData);

        /** @var Collection $quoteCollection */
        $quoteCollection = $this->_objectManager->get(Collection::class);
        /** @var $quote Quote */
        $quote = $quoteCollection->getLastItem();

        /** @var $checkoutSession Session */
        $checkoutSession = $this->_objectManager->get(Session::class);
        $checkoutSession->setQuoteId($quote->getId());
        $checkoutSession->setLoadInactive(true);

        $this->_addresses = $this->_objectManager->create(
            Addresses::class
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Block\Express\Review;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Context;
use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class BillingTest
 */
class BillingTest extends TestCase
{
    const FIXTURE_CUSTOMER_ID = 1;
    const FIXTURE_ADDRESS_ID = 1;
    const SAMPLE_FIRST_NAME = 'UpdatedFirstName';
    const SAMPLE_LAST_NAME = 'UpdatedLastName';
    /** @var Billing */
    protected $_block;
    /** @var AddressRepositoryInterface */
    protected $_addressRepository;
    /** @var AddressFactory */
    protected $_quoteAddressFactory;
    /** @var CustomerRepositoryInterface */
    protected $_customerRepository;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Checkout/_files/quote_with_product_and_payment.php
     */
    public function testGetAddress()
    {
        $addressFromFixture = $this->_addressRepository->getById(self::FIXTURE_ADDRESS_ID);
        $address = $this->_block->getAddress();
        $this->assertEquals($addressFromFixture->getFirstname(), $address->getFirstname());
        $this->assertEquals($addressFromFixture->getLastname(), $address->getLastname());
        $this->assertEquals($addressFromFixture->getCustomerId(), $address->getCustomerId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Checkout/_files/quote_with_product_and_payment.php
     */
    public function testGetAddressNotSetInQuote()
    {
        $this->_updateQuoteCustomerName();
        $address = $this->_block->getAddress();
        //Make sure the data from sample address was set correctly to the block from customer
        $this->assertEquals(self::SAMPLE_FIRST_NAME, $address->getFirstname());
        $this->assertEquals(self::SAMPLE_LAST_NAME, $address->getLastname());
    }

    /**
     * Update Customer name in Quote
     */
    protected function _updateQuoteCustomerName()
    {
        /** @var $emptyAddress Address */
        $emptyAddress = $this->_quoteAddressFactory->create();
        $emptyAddress->setFirstname(null);
        $emptyAddress->setLastname(null);
        $this->_block->getQuote()->setBillingAddress($emptyAddress);
        $customer = $this->_customerRepository->getById(self::FIXTURE_CUSTOMER_ID);
        $customer->setFirstname(
            self::SAMPLE_FIRST_NAME
        )->setLastname(
            self::SAMPLE_LAST_NAME
        );
        $this->_block->getQuote()->setCustomer($customer);
        $this->_block->getQuote()->save();

        $this->assertEquals(self::SAMPLE_FIRST_NAME, $this->_block->getFirstname());
        $this->assertEquals(self::SAMPLE_LAST_NAME, $this->_block->getLastname());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Checkout/_files/quote_with_product_and_payment.php
     */
    public function testGetFirstNameAndLastName()
    {
        $this->_updateQuoteCustomerName();
        //Make sure the data from sample address was set correctly to the block from customer
        $this->assertEquals(self::SAMPLE_FIRST_NAME, $this->_block->getFirstname());
        $this->assertEquals(self::SAMPLE_LAST_NAME, $this->_block->getLastname());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $this->_customerRepository = $objectManager->create(CustomerRepositoryInterface::class);
        $customer = $this->_customerRepository->getById(self::FIXTURE_CUSTOMER_ID);

        $customerSession = $objectManager->get(\Magento\Customer\Model\Session::class);
        $customerSession->setCustomerData($customer);

        $this->_addressRepository = $objectManager->get(AddressRepositoryInterface::class);
        //fetch sample address
        $address = $this->_addressRepository->getById(self::FIXTURE_ADDRESS_ID);

        /** @var Collection $quoteCollection */
        $quoteCollection = $objectManager->get(Collection::class);
        /** @var $quote Quote */
        $quote = $quoteCollection->getLastItem();
        $quote->setCustomer($customer);
        /** @var $quoteAddressFactory AddressFactory */
        $this->_quoteAddressFactory = $objectManager->get(AddressFactory::class);
        $billingAddress = $this->_quoteAddressFactory->create()->importCustomerAddressData($address);
        $quote->setBillingAddress($billingAddress);
        $quote->save();

        /** @var Session $checkoutSession */
        $checkoutSession = $objectManager->get(Session::class);
        $checkoutSession->setQuoteId($quote->getId());
        $checkoutSession->setLoadInactive(true);

        $objectManager->get(\Magento\Framework\App\Http\Context::class)
            ->setValue(Context::CONTEXT_AUTH, true, false);
        $this->_block = $objectManager->get(LayoutInterface::class)
            ->createBlock(
                Billing::class,
                '',
                ['customerSession' => $customerSession, 'resourceSession' => $checkoutSession]
            );
    }
}

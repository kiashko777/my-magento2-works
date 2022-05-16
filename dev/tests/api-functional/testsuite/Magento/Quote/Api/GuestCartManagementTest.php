<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Api;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class GuestCartManagementTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quoteGuestCartManagementV1';
    const RESOURCE_PATH = '/V1/guest-carts/';

    protected $createdQuotes = [];

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function testCreate()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateEmptyCart',
            ],
        ];

        $requestData = ['storeId' => 1];
        $quoteId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue(strlen($quoteId) >= 32);
        $this->createdQuotes[] = $quoteId;
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     */
    public function testAssignCustomer()
    {
        /** @var $quote Quote */
        $quote = $this->objectManager->create(Quote::class)->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMaskFactory = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class);
        $quoteIdMask = $quoteIdMaskFactory->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');
        /** @var $repository CustomerRepositoryInterface */
        $repository = $this->objectManager->create(CustomerRepositoryInterface::class);
        /** @var $customer CustomerInterface */
        $customer = $repository->getById(1);
        $customerId = $customer->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/guest-carts/' . $cartId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $token
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => 'V1',
                'operation' => self::SERVICE_NAME . 'AssignCustomer',
                'token' => $token
            ],
        ];

        $requestData = [
            'cartId' => $cartId,
            'customerId' => $customerId,
            'storeId' => 1,
        ];
        // Cart must be anonymous (see fixture)
        $this->assertEmpty($quote->getCustomerId());

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        // Reload target quote
        $quote = $this->objectManager->create(Quote::class)->load('test01', 'reserved_order_id');
        $this->assertEquals(0, $quote->getCustomerIsGuest());
        $this->assertEquals($customer->getId(), $quote->getCustomerId());
        $this->assertEquals($customer->getFirstname(), $quote->getCustomerFirstname());
        $this->assertEquals($customer->getLastname(), $quote->getCustomerLastname());
        $this->assertNull($quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getId());
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     */
    public function testAssignCustomerThrowsExceptionIfThereIsNoCustomerWithGivenId()
    {
        $this->expectException(Exception::class);

        /** @var $quote Quote */
        $quote = $this->objectManager->create(Quote::class)->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();
        $customerId = 9999;
        $serviceInfo = [
            'soap' => [
                'serviceVersion' => 'V1',
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'AssignCustomer',
            ],
            'rest' => [
                'resourcePath' => '/V1/guest-carts/' . $cartId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];
        $requestData = [
            'cartId' => $cartId,
            'customerId' => $customerId,
            'storeId' => 1,
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     */
    public function testAssignCustomerThrowsExceptionIfThereIsNoCartWithGivenId()
    {
        $this->expectException(Exception::class);

        $cartId = 9999;
        $customerId = 1;
        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => 'V1',
                'operation' => self::SERVICE_NAME . 'AssignCustomer',
            ],
            'rest' => [
                'resourcePath' => '/V1/guest-carts/' . $cartId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];
        $requestData = [
            'cartId' => $cartId,
            'customerId' => $customerId,
            'storeId' => 1,
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote_with_customer.php
     */
    public function testAssignCustomerThrowsExceptionIfTargetCartIsNotAnonymous()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The customer can\'t be assigned to the cart because the cart isn\'t anonymous.');

        /** @var $customer Customer */
        $customer = $this->objectManager->create(Customer::class)->load(1);
        $customerId = $customer->getId();
        /** @var $quote Quote */
        $quote = $this->objectManager->create(Quote::class)->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();

        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');

        $serviceInfo = [
            'rest' => [
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'resourcePath' => '/V1/guest-carts/' . $cartId,
                'token' => $token
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => 'V1',
                'operation' => self::SERVICE_NAME . 'AssignCustomer',
                'token' => $token
            ],
        ];

        $requestData = [
            'cartId' => $cartId,
            'customerId' => $customerId,
            'storeId' => 1,
        ];
        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_items_saved.php
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     */
    public function testAssignCustomerCartMerged()
    {
        /** @var $customer Customer */
        $customer = $this->objectManager->create(Customer::class)->load(1);
        // Customer has a quote with reserved order ID test_order_1 (see fixture)
        /** @var $customerQuote Quote */
        $customerQuote = $this->objectManager->create(Quote::class)
            ->load('test_order_item_with_items', 'reserved_order_id');
        /** @var $quote Quote */
        $quote = $this->objectManager->create(Quote::class)->load('test01', 'reserved_order_id');
        $expectedQuoteItemsQty = $customerQuote->getItemsQty() + $quote->getItemsQty();

        $cartId = $quote->getId();

        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        $customerId = $customer->getId();

        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');
        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'AssignCustomer',
                'serviceVersion' => 'V1',
                'token' => $token
            ],
            'rest' => [
                'resourcePath' => '/V1/guest-carts/' . $cartId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $token
            ],
        ];

        $requestData = [
            'cartId' => $cartId,
            'customerId' => $customerId,
            'storeId' => 1,
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        $mergedQuote = $this->objectManager
            ->create(Quote::class)
            ->load('test01', 'reserved_order_id');

        $this->assertEquals($expectedQuoteItemsQty, $mergedQuote->getItemsQty());
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_check_payment.php
     */
    public function testPlaceOrder()
    {
        /** @var $quote Quote */
        $quote = $this->objectManager->create(Quote::class)
            ->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        $serviceInfo = [
            'soap' => [
                'service' => 'quoteGuestCartManagementV1',
                'operation' => 'quoteGuestCartManagementV1PlaceOrder',
                'serviceVersion' => 'V1',
            ],
            'rest' => [
                'resourcePath' => '/V1/guest-carts/' . $cartId . '/order',
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];

        $orderId = $this->_webApiCall($serviceInfo, ['cartId' => $cartId]);

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->load($orderId);
        $items = $order->getAllItems();
        $this->assertCount(1, $items);
        $this->assertEquals('Simple Products', $items[0]->getName());
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     */
    public function testAssignCustomerByGuestUser()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You don\'t have the correct permissions to assign the customer to the cart.');

        /** @var $quote Quote */
        $quote = $this->objectManager->create(Quote::class)->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMaskFactory = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class);
        $quoteIdMask = $quoteIdMaskFactory->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();
        $repository = $this->objectManager->create(CustomerRepositoryInterface::class);
        /** @var $customer CustomerInterface */
        $customer = $repository->getById(1);
        $customerId = $customer->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/guest-carts/' . $cartId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => 'V1',
                'operation' => self::SERVICE_NAME . 'AssignCustomer',
            ],
        ];

        $requestData = [
            'cartId' => $cartId,
            'customerId' => $customerId,
            'storeId' => 1,
        ];
        // Cart must be anonymous (see fixture)
        $this->assertEmpty($quote->getCustomerId());

        $this->_webApiCall($serviceInfo, $requestData);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function tearDown(): void
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        foreach ($this->createdQuotes as $quoteId) {
            $quote->load($quoteId);
            $quote->delete();
            /** @var QuoteIdMask $quoteIdMask */
            $quoteIdMask = $this->objectManager->create(QuoteIdMask::class);
            $quoteIdMask->load($quoteId, 'quote_id');
            $quoteIdMask->delete();
        }
    }
}

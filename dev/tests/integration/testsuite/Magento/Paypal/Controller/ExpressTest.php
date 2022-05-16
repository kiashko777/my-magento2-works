<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Controller;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\Generic as GenericSession;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Paypal\Model\Api\Nvp;
use Magento\Paypal\Model\Api\Type\Factory as ApiFactory;
use Magento\Paypal\Model\Config;
use Magento\Paypal\Model\Express\Checkout;
use Magento\Paypal\Model\Session as PaypalSession;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Tests of Paypal Express actions
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExpressTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @magentoDataFixture Magento/Paypal/_files/quote_payment.php
     */
    public function testReviewAction()
    {
        $quote = Bootstrap::getObjectManager()->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');
        Bootstrap::getObjectManager()->get(
            Session::class
        )->setQuoteId(
            $quote->getId()
        );

        $this->dispatch('paypal/express/review');

        $html = $this->getResponse()->getBody();
        $this->assertStringContainsString('Simple Products', $html);
        $this->assertStringContainsString('Review', $html);
        $this->assertStringContainsString('/paypal/express/placeOrder/', $html);
    }

    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoConfigFixture current_store paypal/general/business_account merchant_2012050718_biz@example.com
     */
    public function testCancelAction()
    {
        $quote = $this->_objectManager->create(Quote::class);
        $quote->load('100000002', 'reserved_order_id');
        $order = $this->_objectManager->create(Order::class);
        $order->load('100000002', 'increment_id');
        $session = $this->_objectManager->get(Session::class);
        $session->setLoadInactive(true);
        $session->setLastRealOrderId(
            $order->getRealOrderId()
        )->setLastOrderId(
            $order->getId()
        )->setLastQuoteId(
            $order->getQuoteId()
        )->setQuoteId(
            $order->getQuoteId()
        );
        /** @var $paypalSession PaypalSession */
        $paypalSession = $this->_objectManager->get(PaypalSession::class); // @phpstan-ignore-line
        $paypalSession->setExpressCheckoutToken('token');

        $this->dispatch('paypal/express/cancel');

        $order->load('100000002', 'increment_id');
        $this->assertEquals('canceled', $order->getState());
        $this->assertEquals($session->getQuote()->getGrandTotal(), $quote->getGrandTotal());
        $this->assertEquals($session->getQuote()->getItemsCount(), $quote->getItemsCount());
    }

    /**
     * Test ensures only that customer data was copied to quote correctly.
     *
     * Note that test does not verify communication during remote calls to PayPal.
     *
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testStartActionCustomerToQuote()
    {
        $fixtureCustomerId = 1;
        $fixtureCustomerEmail = 'customer@example.com';
        $fixtureCustomerFirstname = 'John';
        $fixtureQuoteReserveId = 'test01';

        /** Preconditions */
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $this->_objectManager->get(CustomerRepositoryInterface::class);
        $customerData = $customerRepository->getById($fixtureCustomerId);
        $customerSession->setCustomerDataObject($customerData);

        /** @var Quote $quote */
        $quote = $this->_objectManager->create(Quote::class);
        $quote->load($fixtureQuoteReserveId, 'reserved_order_id');

        /** @var Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(Session::class);
        $checkoutSession->setQuoteId($quote->getId());

        /** Preconditions check */
        $this->assertNotEquals(
            $fixtureCustomerEmail,
            $quote->getCustomerEmail(),
            "Precondition failed: customer email in quote is invalid."
        );
        $this->assertNotEquals(
            $fixtureCustomerFirstname,
            $quote->getCustomerFirstname(),
            "Precondition failed: customer first name in quote is invalid."
        );

        /** Execute SUT */
        $this->dispatch('paypal/express/start');

        /** Check if customer data was copied to quote correctly */
        /** @var Quote $updatedQuote */
        $updatedQuote = $this->_objectManager->create(Quote::class);
        $updatedQuote->load($fixtureQuoteReserveId, 'reserved_order_id');
        $this->assertEquals(
            $fixtureCustomerEmail,
            $updatedQuote->getCustomer()->getEmail(),
            "Customer email in quote is invalid."
        );
        $this->assertEquals(
            $fixtureCustomerFirstname,
            $updatedQuote->getCustomer()->getFirstname(),
            "Customer first name in quote is invalid."
        );
    }

    /**
     * Test return action with configurable product.
     *
     * @magentoDataFixture Magento/Paypal/_files/quote_express_configurable.php
     */
    public function testReturnAction()
    {
        $quote = $this->_objectManager->create(Quote::class);
        $quote->load('test_cart_with_configurable', 'reserved_order_id');

        $payment = $quote->getPayment();
        $payment->setMethod(Config::METHOD_WPP_EXPRESS)
            ->setAdditionalInformation(Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID, 123);

        $quote->save();

        $this->_objectManager->removeSharedInstance(Session::class);
        $session = $this->_objectManager->get(Session::class);
        $session->setQuoteId($quote->getId());

        $nvpMethods = [
            'setToken',
            'setPayerId',
            'setAmount',
            'setPaymentAction',
            'setNotifyUrl',
            'setInvNum',
            'setCurrencyCode',
            'setPaypalCart',
            'setIsLineItemsEnabled',
            'setAddress',
            'setBillingAddress',
            'callDoExpressCheckoutPayment',
            'callGetExpressCheckoutDetails',
            'getExportedBillingAddress'
        ];

        $nvpMock = $this->getMockBuilder(Nvp::class)
            ->setMethods($nvpMethods)
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($nvpMethods as $method) {
            $nvpMock->method($method)
                ->willReturnSelf();
        }

        $apiFactoryMock = $this->getMockBuilder(ApiFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $apiFactoryMock->method('create')
            ->with(Nvp::class)
            ->willReturn($nvpMock);

        $this->_objectManager->addSharedInstance($apiFactoryMock, ApiFactory::class);

        $sessionMock = $this->getMockBuilder(GenericSession::class)
            ->setMethods(['getExpressCheckoutToken'])
            ->setConstructorArgs(
                [
                    $this->_objectManager->get(Http::class),
                    $this->_objectManager->get(SidResolverInterface::class),
                    $this->_objectManager->get(ConfigInterface::class),
                    $this->_objectManager->get(SaveHandlerInterface::class),
                    $this->_objectManager->get(ValidatorInterface::class),
                    $this->_objectManager->get(StorageInterface::class),
                    $this->_objectManager->get(CookieManagerInterface::class),
                    $this->_objectManager->get(CookieMetadataFactory::class),
                    $this->_objectManager->get(State::class),
                ]
            )
            ->getMock();

        $sessionMock->method('getExpressCheckoutToken')
            ->willReturn(true);

        // @phpstan-ignore-next-line
        $this->_objectManager->addSharedInstance($sessionMock, PaypalSession::class);

        $this->dispatch('paypal/express/returnAction');
        $this->assertRedirect($this->stringContains('checkout/onepage/success'));

        $this->_objectManager->removeSharedInstance(ApiFactory::class);
        // @phpstan-ignore-next-line
        $this->_objectManager->removeSharedInstance(PaypalSession::class);
    }
}

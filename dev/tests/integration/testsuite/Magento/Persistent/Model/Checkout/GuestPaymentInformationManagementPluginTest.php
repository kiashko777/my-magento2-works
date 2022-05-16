<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Persistent\Model\Checkout;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Checkout\Api\TotalsInformationManagementInterface;
use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\BillingAddressManagementInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestPaymentInformationManagementPluginTest extends TestCase
{
    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $persistentSessionHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerFactory;

    /**
     * @var CartItemRepositoryInterface
     */
    protected $cartItemRepository;

    /**
     * @var QuoteIdMask
     */
    protected $quoteIdMaskFactory;

    /**
     * @var PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var BillingAddressManagementInterface
     */
    protected $billingAddressManagement;

    /**
     * @var ShippingAddressManagementInterface
     */
    protected $shippingAddressManagement;

    /**
     * @var ShippingMethodManagementInterface
     */
    protected $shippingEstimateManagement;

    /**
     * @var TotalsInformationManagementInterface
     */
    protected $totalsInformationManagement;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Test builds out a persistent customer shopping cart, emulates a
     * session expiring, and checks out with the persisted cart as a guest.
     *
     * Expected - Order contains guest email, not customer email.
     *
     * @magentoConfigFixture current_store persistent/options/customer 1
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_default 1
     * @magentoConfigFixture current_store payment/substitution/active 1
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testBeforeSavePaymentInformationAndPlaceOrder()
    {
        $guestEmail = 'guest@example.com';

        //Retrieve customer from repository
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $this->objectManager->create(CustomerRepositoryInterface::class);
        $customer = $customerRepository->getById(1);
        $this->customerSession->loginById($customer->getId());

        //Retrieve product from repository
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->getById(1);
        $product->setOptions(null);
        $productRepository->save($product);

        //Add item to newly created customer cart
        $cartId = $this->cartManagement->createEmptyCartForCustomer($customer->getId());
        /** @var CartItemInterface $quoteItem */
        $quoteItem = $this->objectManager->create(CartItemInterface::class);
        $quoteItem->setQuoteId($cartId);
        $quoteItem->setProduct($product);
        $quoteItem->setQty(2);
        $this->cartItemRepository->save($quoteItem);

        //Fill out address data
        /** @var AddressInterface $billingAddress */
        $billingAddress = $this->objectManager->create(AddressInterface::class);
        $billingAddress->setFirstname('guestFirst');
        $billingAddress->setLastname('guestLast');
        $billingAddress->setEmail($guestEmail);
        $billingAddress->setStreet('guestStreet');
        $billingAddress->setCity('Austin');
        $billingAddress->setTelephone('1342587690');
        $billingAddress->setPostcode('14325');
        $billingAddress->setRegionId(12);
        $billingAddress->setCountryId('US');
        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $this->objectManager->create(AddressInterface::class);
        $shippingAddress->setFirstname('guestFirst');
        $shippingAddress->setLastname('guestLast');
        $shippingAddress->setEmail(null);
        $shippingAddress->setStreet('guestStreet');
        $shippingAddress->setCity('Austin');
        $shippingAddress->setTelephone('1342587690');
        $shippingAddress->setPostcode('14325');
        $shippingAddress->setRegionId(12);
        $shippingAddress->setCountryId('US');
        $shippingAddress->setSameAsBilling(true);
        $this->shippingAddressManagement->assign($cartId, $shippingAddress);
        $shippingAddress = $this->shippingAddressManagement->get($cartId);

        //Determine shipping options and collect totals
        /** @var TotalsInformationInterface $totals */
        $totals = $this->objectManager->create(TotalsInformationInterface::class);
        $totals->setAddress($shippingAddress);
        $totals->setShippingCarrierCode('flatrate');
        $totals->setShippingMethodCode('flatrate');
        $this->totalsInformationManagement->calculate($cartId, $totals);

        //Select payment method
        /** @var PaymentInterface $payment */
        $payment = $this->objectManager->create(PaymentInterface::class);
        $payment->setMethod('checkmo');
        $this->paymentMethodManagement->set($cartId, $payment);
        $quote = $this->cartRepository->get($cartId);

        //Verify checkout session contains correct quote data
        $this->checkoutSession->clearQuote();
        $this->checkoutSession->setQuoteId($quote->getId());

        //Set up persistent session data and expire customer session
        $this->persistentSessionHelper->getSession()->setCustomerId($customer->getId())
            ->setPersistentCookie(10000, '');
        $this->persistentSessionHelper->getSession()->removePersistentCookie()->setPersistentCookie(100000000, '');
        $this->customerSession->setIsCustomerEmulated(true)->expireSessionCookie();

        //Grab masked quote Id to pass to payment manager
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load(
            $this->checkoutSession->getQuote()->getId(),
            'quote_id'
        );
        $maskedCartId = $quoteIdMask->getMaskedId();

        //Submit order as expired/emulated customer
        /** @var GuestPaymentInformationManagement $paymentManagement */
        $paymentManagement = $this->objectManager->create(
            GuestPaymentInformationManagement::class
        );

        //Grab created order data
        $orderId = $paymentManagement->savePaymentInformationAndPlaceOrder(
            $maskedCartId,
            $guestEmail,
            $quote->getPayment(),
            $billingAddress
        );
        /** @var OrderRepositoryInterface $orderRepo */
        $orderRepo = $this->objectManager->create(OrderRepositoryInterface::class);
        $order = $orderRepo->get($orderId);

        //Assert order tied to guest email
        $this->assertEquals($guestEmail, $order->getCustomerEmail());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->customerSession = $this->objectManager->get(\Magento\Customer\Model\Session::class);
        $this->persistentSessionHelper = $this->objectManager->create(\Magento\Persistent\Helper\Session::class);
        $this->customerFactory = $this->objectManager->create(
            CustomerFactory::class
        );
        $this->checkoutSession = $this->objectManager->create(Session::class);
        $this->cartRepository = $this->objectManager->create(CartRepositoryInterface::class);
        $this->cartManagement = $this->objectManager->create(CartManagementInterface::class);
        $this->cartItemRepository = $this->objectManager->create(CartItemRepositoryInterface::class);
        $this->quoteIdMaskFactory = $this->objectManager->create(QuoteIdMaskFactory::class);
        $this->paymentMethodManagement = $this->objectManager->create(
            PaymentMethodManagementInterface::class
        );
        $this->billingAddressManagement = $this->objectManager->create(
            BillingAddressManagementInterface::class
        );
        $this->shippingEstimateManagement = $this->objectManager->create(
            ShippingMethodManagementInterface::class
        );
        $this->totalsInformationManagement = $this->objectManager->create(
            TotalsInformationManagementInterface::class
        );
        $this->shippingAddressManagement = $this->objectManager->create(
            ShippingAddressManagementInterface::class
        );
    }
}

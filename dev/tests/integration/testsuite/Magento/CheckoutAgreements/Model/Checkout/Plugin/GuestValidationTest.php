<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CheckoutAgreements\Model\Checkout\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Checkout\Api\TotalsInformationManagementInterface;
use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Checkout\Model\Session;
use Magento\CheckoutAgreements\Model\Agreement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\PaymentExtension;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestValidationTest extends TestCase
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * @var QuoteIdMask
     */
    private $quoteIdMaskFactory;

    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var ShippingAddressManagementInterface
     */
    private $shippingAddressManagement;

    /**
     * @var TotalsInformationManagementInterface
     */
    private $totalsInformationManagement;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Expected - Order fail with exception.
     *
     * @magentoConfigFixture current_store payment/substitution/active 1
     * @magentoConfigFixture default_store checkout/options/enable_agreements 1
     * @magentoDataFixture Magento/CheckoutAgreements/_files/multi_agreements_active_with_text.php
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation enabled
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @dataProvider dataProvider
     * @param string[] $agreementNames
     */
    public function testBeforeSavePaymentInformationAndPlaceOrder($agreementNames)
    {
        $guestEmail = 'guest@example.com';
        $carrierCode = 'flatrate';
        $shippingMethodCode = 'flatrate';
        $paymentMethod = 'checkmo';
        $paymentExtension = $this->getPaymentExtension($agreementNames);
        $product = $this->getProduct(1);
        $shippingAddress = $this->getShippingAddress();
        $billingAddress = $this->getBillingAddress($guestEmail);
        $payment = $this->getPayment($paymentMethod, $paymentExtension);

        //Create cart and add product to it
        $cartId = $this->cartManagement->createEmptyCart();
        $this->addProductToCart($product, $cartId);

        //Assign shipping address
        $this->shippingAddressManagement->assign($cartId, $shippingAddress);
        $shippingAddress = $this->shippingAddressManagement->get($cartId);

        //Calculate totals
        $totals = $this->getTotals($shippingAddress, $carrierCode, $shippingMethodCode);
        $this->totalsInformationManagement->calculate($cartId, $totals);

        //Set payment method
        $this->paymentMethodManagement->set($cartId, $payment);

        //Verify checkout session contains correct quote data
        $quote = $this->cartRepository->get($cartId);
        $this->checkoutSession->clearQuote();
        $this->checkoutSession->setQuoteId($quote->getId());

        //Grab masked quote Id to pass to payment manager
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load(
            $this->checkoutSession->getQuote()->getId(),
            'quote_id'
        );
        $maskedCartId = $quoteIdMask->getMaskedId();

        $paymentManagement = $this->objectManager->create(
            GuestPaymentInformationManagement::class
        );

        try {
            $orderId = $paymentManagement->savePaymentInformationAndPlaceOrder(
                $maskedCartId,
                $guestEmail,
                $payment,
                $billingAddress
            );
            $this->assertNotNull($orderId);
        } catch (CouldNotSaveException $e) {
            $this->assertEquals(
                __(
                    "The order wasn't placed. "
                    . "First, agree to the terms and conditions, then try placing your order again."
                ),
                $e->getMessage()
            );
        }
    }

    /**
     * @param string[] $agreementNames
     * @return PaymentExtension
     */
    private function getPaymentExtension($agreementNames)
    {
        $agreementIds = [];
        foreach ($agreementNames as $agreementName) {
            $agreementIds[] = $this->getAgreement($agreementName)->getAgreementId();
        }
        $extension = $this->objectManager->get(PaymentExtension::class);
        $extension->setAgreementIds($agreementIds);
        return $extension;
    }

    /**
     * @param string $agreementName
     * @return Agreement
     */
    private function getAgreement($agreementName)
    {
        $agreement = $this->objectManager->create(Agreement::class);
        $agreement->load($agreementName, 'name');
        return $agreement;
    }

    /**
     * @param int $productId
     * @return ProductInterface
     */
    private function getProduct($productId)
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->getById($productId);
        $product->setOptions(null);
        $productRepository->save($product);
        return $product;
    }

    /**
     * @return AddressInterface
     */
    private function getShippingAddress()
    {
        $shippingAddress = $this->objectManager->create(AddressInterface::class);
        $shippingAddress->setFirstname('First');
        $shippingAddress->setLastname('Last');
        $shippingAddress->setEmail(null);
        $shippingAddress->setStreet('Street');
        $shippingAddress->setCity('City');
        $shippingAddress->setTelephone('1234567890');
        $shippingAddress->setPostcode('12345');
        $shippingAddress->setRegionId(12);
        $shippingAddress->setCountryId('US');
        $shippingAddress->setSameAsBilling(true);
        return $shippingAddress;
    }

    /**
     * @param string $guestEmail
     * @return AddressInterface
     */
    private function getBillingAddress($guestEmail)
    {
        /** @var AddressInterface $billingAddress */
        $billingAddress = $this->objectManager->create(AddressInterface::class);
        $billingAddress->setFirstname('First');
        $billingAddress->setLastname('Last');
        $billingAddress->setEmail($guestEmail);
        $billingAddress->setStreet('Street');
        $billingAddress->setCity('City');
        $billingAddress->setTelephone('1234567890');
        $billingAddress->setPostcode('12345');
        $billingAddress->setRegionId(12);
        $billingAddress->setCountryId('US');
        return $billingAddress;
    }

    /**
     * @param $paymentMethod
     * @param $paymentExtension
     * @return PaymentInterface
     */
    private function getPayment($paymentMethod, $paymentExtension)
    {
        $payment = $this->objectManager->create(PaymentInterface::class);
        $payment->setMethod($paymentMethod);
        $payment->setExtensionAttributes($paymentExtension);
        return $payment;
    }

    /**
     * @param ProductInterface $product
     * @param string $cartId
     */
    private function addProductToCart($product, $cartId)
    {
        /** @var CartItemInterface $quoteItem */
        $quoteItem = $this->objectManager->create(CartItemInterface::class);
        $quoteItem->setQuoteId($cartId);
        $quoteItem->setProduct($product);
        $quoteItem->setQty(2);
        $this->cartItemRepository->save($quoteItem);
    }

    /**
     * @param AddressInterface $shippingAddress
     * @param string $carrierCode
     * @param string $methodCode
     * @return TotalsInformationInterface
     */
    private function getTotals($shippingAddress, $carrierCode, $methodCode)
    {
        /** @var TotalsInformationInterface $totals */
        $totals = $this->objectManager->create(TotalsInformationInterface::class);
        $totals->setAddress($shippingAddress);
        $totals->setShippingCarrierCode($carrierCode);
        $totals->setShippingMethodCode($methodCode);

        return $totals;
    }

    public function dataProvider()
    {
        return [
            [[]],
            [['First Checkout Agreement (active)']],
            [['First Checkout Agreement (active)', 'Second Checkout Agreement (active)']]
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->checkoutSession = $this->objectManager->create(Session::class);
        $this->cartRepository = $this->objectManager->create(CartRepositoryInterface::class);
        $this->cartManagement = $this->objectManager->create(CartManagementInterface::class);
        $this->cartItemRepository = $this->objectManager->create(CartItemRepositoryInterface::class);
        $this->quoteIdMaskFactory = $this->objectManager->create(QuoteIdMaskFactory::class);
        $this->paymentMethodManagement = $this->objectManager->create(
            PaymentMethodManagementInterface::class
        );
        $this->totalsInformationManagement = $this->objectManager->create(
            TotalsInformationManagementInterface::class
        );
        $this->shippingAddressManagement = $this->objectManager->create(
            ShippingAddressManagementInterface::class
        );
    }
}

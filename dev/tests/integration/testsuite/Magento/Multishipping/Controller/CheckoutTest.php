<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Multishipping\Controller;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\TestCase\AbstractController;
use Psr\Log\LoggerInterface;

/**
 * Test class for \Magento\Multishipping\Controller\Checkout
 *
 * @magentoAppArea frontend
 * @magentoDataFixture Magento/Sales/_files/quote.php
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class CheckoutTest extends AbstractController
{
    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Covers \Magento\Multishipping\Block\Checkout\Payment\Info and \Magento\Multishipping\Block\Checkout\Overview
     *
     * @magentoConfigFixture current_store multishipping/options/checkout_multiple 1
     */
    public function testOverviewAction()
    {
        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        $logger = $this->createMock(LoggerInterface::class);
        /** @var AccountManagementInterface $service */
        $service = $this->_objectManager->create(AccountManagementInterface::class);
        $customer = $service->authenticate('customer@example.com', 'password');
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->create(\Magento\Customer\Model\Session::class, [$logger]);
        $customerSession->setCustomerDataAsLoggedIn($customer);
        $this->checkoutSession->setCheckoutState(State::STEP_BILLING);
        $this->getRequest()->setPostValue('payment', ['method' => 'checkmo']);
        $this->dispatch('multishipping/checkout/overview');
        $html = $this->getResponse()->getBody();
        $this->assertStringContainsString('<div class="box box-billing-method">', $html);
        $this->assertStringContainsString('<div class="box box-shipping-method">', $html);
        $this->assertStringContainsString(
            '<dt class="title">' . $this->quote->getPayment()->getMethodInstance()->getTitle() . '</dt>',
            $html
        );
        $this->assertStringContainsString('<span class="price">$10.00</span>', $html);
        $this->assertStringContainsString(
            '<input name="form_key" type="hidden" value="' . $formKey->getFormKey(),
            $html
        );
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->quote = $this->_objectManager->create(Quote::class);
        $this->checkoutSession = $this->_objectManager->get(Session::class);

        $this->quote->load('test01', 'reserved_order_id');
        $this->checkoutSession->setQuoteId($this->quote->getId());
        $this->checkoutSession->setCartWasUpdated(false);
    }
}

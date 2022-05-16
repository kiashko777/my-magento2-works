<?php
/**
 * Test for \Magento\Paypal\Block\Billing\Agreement\View
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Block\Billing\Agreement;

use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Paypal\Model\Billing\Agreement;
use Magento\Paypal\Model\ResourceModel\Billing\Agreement\Collection;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

class ViewTest extends AbstractBackendController
{
    /** @var View */
    protected $_block;

    /**
     * Test getting orders associated with specified billing agreement.
     *
     * Create two identical orders, associate one of them with billing agreement and invoke testGetRelatedOrders()
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetRelatedOrders()
    {
        /** Customer ID declared in the fixture */
        $customerId = 1;
        /** Assign first order to the active customer */
        /** @var Order $orderA */
        $orderA = Bootstrap::getObjectManager()->create(Order::class);
        $orderA->loadByIncrementId('100000001');
        $orderA->setCustomerIsGuest(false)->setCustomerId($customerId)->save();
        /** @var Session $customerSession */
        $customerSession = Bootstrap::getObjectManager()->create(Session::class);
        $customerSession->setCustomerId($customerId);

        /** Assign second order to the active customer */
        $orderB = clone $orderA;
        $orderB->setId(
            null
        )->setIncrementId(
            '100000002'
        )->setCustomerIsGuest(
            false
        )->setCustomerId(
            $customerId
        )->save();

        /** @var Session $customerSession */
        $customerSession = Bootstrap::getObjectManager()->create(Session::class);
        $customerSession->setCustomerId($customerId);

        /** @var Collection $billingAgreementCollection */
        $billingAgreementCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        /** @var Agreement $billingAgreement */
        $billingAgreement = $billingAgreementCollection->getFirstItem();
        $billingAgreement->addOrderRelation($orderA->getId())->save();

        $registry = Bootstrap::getObjectManager()->get(Registry::class);
        $registry->register('current_billing_agreement', $billingAgreement);

        $relatedOrders = $this->_block->getRelatedOrders();
        $this->assertEquals(1, $relatedOrders->count(), "Only one order must be returned.");
        $this->assertEquals(
            $orderA->getId(),
            $relatedOrders->getFirstItem()->getId(),
            "Invalid order returned as associated with billing agreement."
        );
    }

    protected function setUp(): void
    {
        $this->_block = Bootstrap::getObjectManager()->create(View::class);
        parent::setUp();
    }
}

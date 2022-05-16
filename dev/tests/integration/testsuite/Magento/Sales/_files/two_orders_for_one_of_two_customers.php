<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/two_customers.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Order $order */
$order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

$order->setCustomerId(1)->setCustomerIsGuest(false)->save();

$orderItems = $order->getItems();
$orderItem = reset($orderItems);
$billingAddress = $order->getBillingAddress();
$shippingAddress = $order->getShippingAddress();
$payment2 = $objectManager->create(Payment::class);
$payment2->setMethod('checkmo');

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId('100000002')
    ->setState(
        Order::STATE_PROCESSING
    )->setStatus(
        $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
    )->setSubtotal(
        100
    )->setBaseSubtotal(
        100
    )->setBaseGrandTotal(
        100
    )->setCustomerIsGuest(
        true
    )->setCustomerEmail(
        'customer2@null.com'
    )->setBillingAddress(
        $billingAddress
    )->setShippingAddress(
        $shippingAddress
    )->setStoreId(
        $objectManager->get(StoreManagerInterface::class)->getStore()->getId()
    )->addItem(
        $orderItem
    )->setPayment(
        $payment2
    );

$order->save();

$order->setCustomerId(1)->setCustomerIsGuest(false)->save();

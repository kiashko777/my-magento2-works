<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/customers.php');

$addressData = include __DIR__ . '/../../../Magento/Sales/_files/address_data.php';

$objectManager = Bootstrap::getObjectManager();

$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');
$payment->setAdditionalInformation('last_trans_id', '11122');
$payment->setAdditionalInformation('metadata', [
    'type' => 'free',
    'fraudulent' => false
]);

/** @var Order $order */
$order = $objectManager->create(Order::class);

$order->setIncrementId(
    '100000001'
)->setState(
    Order::STATE_PROCESSING
)->setStatus(
    $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
)->setSubtotal(
    100
)->setGrandTotal(
    100
)->setBaseSubtotal(
    100
)->setBaseGrandTotal(
    100
)->setCustomerIsGuest(
    true
)->setCustomerId(
    null
)->setCustomerEmail(
    'unknown@example.com'
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->setStoreId(
    $objectManager->get(StoreManagerInterface::class)->getStore()->getId()
)->setPayment(
    $payment
);
$order->isObjectNew(true);
$order->save();


$order->setIncrementId(
    '100000002'
)->setState(
    Order::STATE_PROCESSING
)->setStatus(
    $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
)->setSubtotal(
    100
)->setGrandTotal(
    100
)->setBaseSubtotal(
    100
)->setBaseGrandTotal(
    100
)->setCustomerIsGuest(
    false
)->setCustomerId(
    1
)->setCustomerEmail(
    'customer1@example.com'
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->setStoreId(
    $objectManager->get(StoreManagerInterface::class)->getStore()->getId()
)->setPayment(
    $payment
);
$order->isObjectNew(true);
$order->save();


$order->setIncrementId(
    '100000003'
)->setState(
    Order::STATE_PROCESSING
)->setStatus(
    $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
)->setSubtotal(
    100
)->setGrandTotal(
    100
)->setBaseSubtotal(
    100
)->setBaseGrandTotal(
    100
)->setCustomerIsGuest(
    false
)->setCustomerId(
    2
)->setCustomerEmail(
    'customer2@example.com'
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->setStoreId(
    $objectManager->get(StoreManagerInterface::class)->getStore()->getId()
)->setPayment(
    $payment
);
$order->isObjectNew(true);
$order->save();

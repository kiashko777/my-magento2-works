<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

$objectManager = Bootstrap::getObjectManager();

$addressData = include __DIR__ . '/../../../Magento/Sales/_files/address_data.php';

$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');

/** @var $product Product */
$product = $objectManager->create(Product::class);
$repository = $objectManager->create(ProductRepository::class);
$product = $repository->get('simple');

$optionValuesByType = [
    'field' => 'Test value',
    'date_time' => [
        'month' => '3',
        'day' => '5',
        'year' => '2020',
        'hour' => '2',
        'minute' => '15',
        'day_part' => 'am',
        'date_internal' => '2020-09-30 02:15:00'
    ],
    'drop_down' => '3-1-select',
    'radio' => '4-1-radio',
];
$optionsDate = [
    [
        'label' => 'date',
        'value' => 'Mar 5, 2020',
        'print_value' => 'Mar 5, 2020',
        'option_id' => '1',
        'option_type' => 'date',
        'option_value' => '2020-03-05 00:00:00',
        'custom_view' => '',
    ]
];

$requestInfo = ['options' => []];
$productOptions = $product->getOptions();
foreach ($productOptions as $option) {
    $requestInfo['options'][$option->getOptionId()] = $optionValuesByType[$option->getType()];
}

/** @var Item $orderItem */
$orderItem = $objectManager->create(Item::class);
$orderItem->setProductId($product->getId());
$orderItem->setSku($product->getSku());
$orderItem->setQtyOrdered(1);
$orderItem->setBasePrice($product->getPrice());
$orderItem->setPrice($product->getPrice());
$orderItem->setRowTotal($product->getPrice());
$orderItem->setProductType($product->getTypeId());
$orderItem->setProductOptions([
    'info_buyRequest' => $requestInfo,
    'options' => $optionsDate,
]);

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId('100000001');
$order->setState(Order::STATE_NEW);
$order->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_NEW));
$order->setCustomerIsGuest(true);
$order->setCustomerEmail('customer@example.com');
$order->setCustomerFirstname('firstname');
$order->setCustomerLastname('lastname');
$order->setBillingAddress($billingAddress);
$order->setShippingAddress($shippingAddress);
$order->setAddresses([$billingAddress, $shippingAddress]);
$order->setPayment($payment);
$order->addItem($orderItem);
$order->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId());
$order->setSubtotal(100);
$order->setBaseSubtotal(100);
$order->setBaseGrandTotal(100);
$order->setCustomerId(1)
    ->setCustomerIsGuest(false)
    ->save();

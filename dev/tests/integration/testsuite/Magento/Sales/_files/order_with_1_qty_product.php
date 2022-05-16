<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_without_custom_options.php');

$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
$secondProduct = $productRepository->get('simple-2');

$addressData = include __DIR__ . '/../../../Magento/Sales/_files/address_data.php';
$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');
$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');
$customerIdFromFixture = 1;

$optionValuesByType = [
    'field' => 'Test value',
    'date_time' => [
        'year' => '2015',
        'month' => '9',
        'day' => '9',
        'hour' => '2',
        'minute' => '2',
        'day_part' => 'am',
        'date_internal' => '',
    ],
    'drop_down' => '3-1-select',
    'radio' => '4-1-radio',
];

$requestInfo = ['options' => [], 'qty' => 1];
$productOptions = $product->getOptions();
foreach ($productOptions as $option) {
    $requestInfo['options'][$option->getOptionId()] = $optionValuesByType[$option->getType()];
}

/** @var Item $orderItem */
$orderItem = $objectManager->create(Item::class);
$orderItem->setProductId($product->getId());
$orderItem->setQtyOrdered(10);
$orderItem->setBasePrice($product->getPrice());
$orderItem->setPrice($product->getPrice());
$orderItem->setRowTotal($product->getPrice());
$orderItem->setProductType($product->getTypeId());
$orderItem->setProductOptions(['info_buyRequest' => $requestInfo]);
$orderItem->setName($product->getName());
$orderItem->setSku($product->getSku());
$orderItem->setStoreId(0);
// create second order item

$orderItem2 = $objectManager->create(Item::class);
$requestInfo = [
    'qty' => 1
];
$orderItem2->setProductId($secondProduct->getId())
    ->setQtyOrdered(10)
    ->setBasePrice($secondProduct->getPrice())
    ->setPrice($secondProduct->getPrice())
    ->setRowTotal($secondProduct->getPrice())
    ->setProductType($secondProduct->getTypeId())
    ->setName($secondProduct->getName())
    ->setSku($secondProduct->getSku())
    ->setStoreId(0)
    ->setProductId($secondProduct->getId())
    ->setSku($secondProduct->getSku())
    ->setProductOptions(['info_buyRequest' => $requestInfo]);

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId('100000001');
$order->setState(Order::STATE_NEW);
$order->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_NEW));
$order->setCustomerIsGuest(false);
$order->setCustomerId($customer->getId());
$order->setCustomerEmail($customer->getEmail());
$order->setCustomerFirstname($customer->getName());
$order->setCustomerLastname($customer->getLastname());
$order->setBillingAddress($billingAddress);
$order->setShippingAddress($shippingAddress);
$order->setAddresses([$billingAddress, $shippingAddress]);
$order->setPayment($payment);
$order->addItem($orderItem);
$order->addItem($orderItem2);
$order->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId());
$order->setSubtotal(100);
$order->setBaseSubtotal(100);
$order->setBaseGrandTotal(100);
$order->setCustomerId($customerIdFromFixture);
$order->setCustomerIsGuest(false);
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
$orderRepository->save($order);

// load product and set qty to 1
/** @var ProductRepositoryInterface $repository */
$productRepository = Bootstrap::getObjectManager()->create(ProductRepositoryInterface::class);
$productSku = 'simple';
/** @var Product $product */
$product = $productRepository->get($productSku);
// set product qty to 1
$product->setStockData(
    [
        'use_config_manage_stock' => 1,
        'qty' => 1,
    ]
);
$productRepository->save($product);

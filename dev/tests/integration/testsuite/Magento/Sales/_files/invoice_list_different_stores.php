<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/default_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');
/** @var Product $product */
Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_store.php');

$addressData = include __DIR__ . '/address_data.php';
$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
$store = Bootstrap::getObjectManager()
    ->create(Store::class);
$secondStoreId = $store->load('fixture_second_store')->getId();

$orders = [
    [
        'increment_id' => '100000002',
        'state' => Order::STATE_NEW,
        'status' => 'processing',
        'grand_total' => 120.00,
        'subtotal' => 120.00,
        'base_grand_total' => 120.00,
        'store_id' => 0,
        'website_id' => 1,
    ],
    [
        'increment_id' => '100000003',
        'state' => Order::STATE_PROCESSING,
        'status' => 'processing',
        'grand_total' => 140.00,
        'base_grand_total' => 140.00,
        'subtotal' => 140.00,
        'store_id' => 1,
        'website_id' => 0,
    ],
    [
        'increment_id' => '100000004',
        'state' => Order::STATE_PROCESSING,
        'status' => 'closed',
        'grand_total' => 140.00,
        'base_grand_total' => 140.00,
        'subtotal' => 140.00,
        'store_id' => $secondStoreId,
        'website_id' => 1,
    ],
];

/** @var Address $billingAddress */
$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');

/** @var Address $shippingAddress */
$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

/** @var Payment $payment */
$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');
$payment->setAdditionalInformation('last_trans_id', '11122');
$payment->setAdditionalInformation('metadata', [
    'type' => 'free',
    'fraudulent' => false,
]);

/** @var Item $orderItem */
$orderItem = $objectManager->create(Item::class);
$orderItem->setProductId($product->getId())->setQtyOrdered(2);
$orderItem->setBasePrice($product->getPrice());
$orderItem->setPrice($product->getPrice());
$orderItem->setRowTotal($product->getPrice());
$orderItem->setProductType('simple');

/** @var InvoiceManagementInterface $orderService */
$orderService = ObjectManager::getInstance()->create(
    InvoiceManagementInterface::class
);

/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);

foreach ($orders as $orderFixture) {
    /** @var Order $order */
    $order = $objectManager->create(Order::class);
    $order->setData($orderFixture);
    $order->setIncrementId(
        $orderFixture['increment_id']
    )->setStoreId(
        $orderFixture['store_id']
    )->setState(
        $orderFixture['state']
    )->setStatus(
        $orderFixture['status']
    )->setSubtotal(
        $orderFixture['subtotal']
    )->setGrandTotal(
        $orderFixture['grand_total']
    )->setBaseSubtotal(
        $orderFixture['subtotal']
    )->setBaseGrandTotal(
        $orderFixture['base_grand_total']
    )->setCustomerIsGuest(
        true
    )->setCustomerEmail(
        'customer@null.com'
    )->setBillingAddress(
        clone $billingAddress
    )->setShippingAddress(
        clone $shippingAddress
    )->addItem(
        clone $orderItem
    )->setPayment(
        clone $payment
    );

    $orderRepository->save($order);

    /** @var Invoice $invoice */
    $invoice = $orderService->prepareInvoice($order, $order->getItems());
    $invoice->register();
    $invoice->setSendEmail(1);
    $invoice->setStoreId($orderFixture['store_id']);
    $order = $invoice->getOrder();
    $order->setIsInProcess(true);
    $transactionSave = Bootstrap::getObjectManager()
        ->create(Transaction::class);
    $transactionSave->addObject($invoice)->addObject($order)->save();
}

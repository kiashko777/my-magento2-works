<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\TestFramework\Helper\Bootstrap;

$billingAddress = Bootstrap::getObjectManager()->create(
    Address::class,
    [
        'data' => [
            AddressInterface::KEY_TELEPHONE => 3468676,
            AddressInterface::KEY_POSTCODE => 75477,
            AddressInterface::KEY_COUNTRY_ID => 'US',
            AddressInterface::KEY_CITY => 'CityM',
            AddressInterface::KEY_COMPANY => 'CompanyName',
            AddressInterface::KEY_STREET => 'Green str, 67',
            AddressInterface::KEY_LASTNAME => 'Smith',
            AddressInterface::KEY_FIRSTNAME => 'John',
            AddressInterface::KEY_REGION_ID => 1,
        ]
    ]
);
$billingAddress->setAddressType('billing');

$payment = Bootstrap::getObjectManager()->create(
    Payment::class
);
$payment->setMethod('checkmo');

/** @var Item $orderItem */
$orderItem = Bootstrap::getObjectManager()->create(
    Item::class
);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
$product = $productRepository->getById(1);
$link = $product->getExtensionAttributes()->getDownloadableProductLinks()[0];

$orderItem->setProductId(1)
    ->setProductType(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
    ->setName('Downloadable Products')
    ->setProductOptions(['links' => [$link->getId()]])
    ->setBasePrice(100)
    ->setPrice(10)
    ->setSku('downloadable-product')
    ->setQtyOrdered(1);

$order = Bootstrap::getObjectManager()->create(Order::class);
$order->setCustomerEmail('customer@example.com')
    ->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setCustomerId(1)
    ->setStoreId(1)
    ->setEmailSent(1)
    ->setBillingAddress($billingAddress)
    ->setPayment($payment);
$order->save();

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

Bootstrap::getInstance()->loadArea(
    FrontNameResolver::AREA_CODE
);

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId('virtual')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setStockData([
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ])
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->save();
$product->load(1);

$addressData = include __DIR__ . '/address_data.php';

$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');

/** @var $quote Quote */
$quote = $objectManager->create(Quote::class);
$quote->setCustomerEmail('admin@example.com');
$quote->setCustomerIsGuest(true);
$quote->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId());
$quote->setReservedOrderId('100000001');
$quote->setBillingAddress($billingAddress);
$quote->setShippingAddress($shippingAddress);
$quote->getPayment()->setMethod('checkmo');
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->getShippingAddress()->collectShippingRates();

/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->create(CartRepositoryInterface::class);
$quoteRepository->save($quote);

/** @var CartItemInterfaceFactory $cartItemFactory */
$cartItemFactory = $objectManager->get(CartItemInterfaceFactory::class);

/** @var CartItemInterface $cartItem */
$cartItem = $cartItemFactory->create();
$cartItem->setQty(10);
$cartItem->setQuoteId($quote->getId());
$cartItem->setSku($product->getSku());
$cartItem->setProductType(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);

/** @var CartItemRepositoryInterface $cartItemRepository */
$cartItemRepository = $objectManager->get(CartItemRepositoryInterface::class);
$cartItemRepository->save($cartItem);

/** @var CartManagementInterface $quoteManagement */
$quoteManagement = $objectManager->create(CartManagementInterface::class);

$quote = $quoteRepository->get($quote->getId());
$order = $quoteManagement->submit($quote, ['increment_id' => '100000001']);

/** @var $item Item */
$item = $order->getAllItems()[0];

/** @var InvoiceFactory $invoiceFactory */
$invoiceFactory = $objectManager->get(InvoiceManagementInterface::class);

/** @var $invoice Invoice */
$invoice = $invoiceFactory->prepareInvoice($order, [$item->getId() => 10]);
$invoice->register();
$invoice->save();
$order->save();

$invoice = $objectManager->get(InvoiceRepositoryInterface::class)->get($invoice->getId());

/** @var CreditmemoFactory $creditmemoFactory */
$creditmemoFactory = $objectManager->get(CreditmemoFactory::class);
$creditmemo = $creditmemoFactory->createByInvoice($invoice, ['qtys' => [$item->getId() => 5]]);

foreach ($creditmemo->getAllItems() as $creditmemoItem) {
    //Workaround to return items to stock
    $creditmemoItem->setBackToStock(true);
}

$creditmemoManagement = $objectManager->create(CreditmemoManagementInterface::class);
$creditmemoManagement->refund($creditmemo);

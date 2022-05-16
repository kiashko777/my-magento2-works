<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/product_configurable.php');

$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = $objectManager->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$configurableProduct = $productRepository->get('configurable');

/** \Magento\Customer\Model\Customer $customer */
$addressData = include __DIR__ . '/../../../Magento/Sales/_files/address_data.php';
$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');
$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$objectManager = Bootstrap::getObjectManager();
$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');
$customerIdFromFixture = 1;

// configurable product
/** @var Config $eavConfig */
$eavConfig = $objectManager->get(Config::class);
/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = $eavConfig->getAttribute('catalog_product', 'test_configurable');

/** @var $options Collection */
$options = $objectManager->create(Collection::class);
$configurableOptions = $options->setAttributeFilter($attribute->getId())->getItems();
foreach ($configurableOptions as $option) {
    $requestInfo[] = [
        'qty' => 1,
        'super_attribute' => [
            $attribute->getId() => $option->getId(),
        ],
    ];
}
$qtyOrdered = 1;
/** @var Item $orderItem */
$orderConfigurableItem = $objectManager->create(Item::class);
$orderConfigurableItem->setProductId($configurableProduct->getId())->setQtyOrdered($qtyOrdered);
$orderConfigurableItem->setBasePrice($configurableProduct->getPrice());
$orderConfigurableItem->setPrice($configurableProduct->getPrice());
$orderConfigurableItem->setRowTotal($configurableProduct->getPrice());
$orderConfigurableItem->setParentItemId(null);
$orderConfigurableItem->setProductType('configurable');
$configurableVariations = [];
$producLinks = array_values($configurableProduct->getExtensionAttributes()->getConfigurableProductLinks());
foreach ($producLinks as $key => $variationId) {
    $simpleProductId = current($configurableProduct->getExtensionAttributes()->getConfigurableProductLinks());

    /** @var ProductInterface $simpleProduct */
    $simpleProduct = $productRepository->getById($simpleProductId);

    $info = $requestInfo[$key];
    $info['product'] = $simpleProductId;
    $info['item'] = $simpleProduct;

    $orderConfigurableParentItem = clone $orderConfigurableItem;
    $orderConfigurableParentItem->setProductOptions(['info_buyRequest' => $info]);
    $configurableItems[] = $orderConfigurableParentItem;
}

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
$order->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId());
$order->setSubtotal(100);
$order->setBaseSubtotal(100);
$order->setBaseGrandTotal(100);
$order->setCustomerId($customerIdFromFixture)->setCustomerIsGuest(false);
foreach ($configurableItems as $item) {
    $order->addItem($item);
}
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
$orderRepository->save($order);

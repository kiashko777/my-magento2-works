<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Bundle\Model\Option;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Downloadable\Model\Link;
use Magento\Downloadable\Model\ResourceModel\Link\Collection;
use Magento\Eav\Model\Config;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = $objectManager->create(CustomerRegistry::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
$customer = $customerRegistry->retrieve(1);
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_without_custom_options.php');
$simpleProduct = $productRepository->get('simple-2');
Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/product_configurable.php');
$configurableProduct = $productRepository->get('configurable');
// this change is require for compatibility with downloadable product fixture,
// it sets id=1 for product that causes rewrite of configurable product
$configurableProduct->setId(111);
$configurableProduct->setUrlKey('configurable-product-new');
$productRepository->save($configurableProduct);
Resolver::getInstance()->requireDataFixture('Magento/GraphQl/Catalog/_files/virtual_product.php');
$virtualProduct = $productRepository->get('virtual_product');
Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/bundle_product_radio_required_option.php');
$bundleProduct = $productRepository->get('bundle-product-radio-required-option');
Resolver::getInstance()->requireDataFixture('Magento/Downloadable/_files/product_downloadable.php');
$downloadableProduct = $productRepository->get('downloadable-product');

/** \Magento\Customer\Model\Customer $customer */
$addressData = include __DIR__ . '/../../../Magento/Sales/_files/address_data.php';
$billingAddress = $objectManager->create(Address::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');
$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');
$customerIdFromFixture = 1;

/**
 * simple product
 */
$simpleProductItem = $objectManager->create(Item::class);
$requestInfo = [
    'qty' => 1
];
$simpleProductItem->setProductId($simpleProduct->getId())
    ->setQtyOrdered(1)
    ->setBasePrice($simpleProduct->getPrice())
    ->setPrice($simpleProduct->getPrice())
    ->setRowTotal($simpleProduct->getPrice())
    ->setProductType($simpleProduct->getTypeId())
    ->setName($simpleProduct->getName())
    ->setSku($simpleProduct->getSku())
    ->setStoreId(0)
    ->setProductOptions(['info_buyRequest' => $requestInfo]);

/**
 * configurable product
 */
/** @var Config $eavConfig */
$eavConfig = $objectManager->get(Config::class);
/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = $eavConfig->getAttribute('catalog_product', 'test_configurable');

/** @var $options \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection */
$options = $objectManager->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection::class);
$option = $options->setAttributeFilter($attribute->getId())
    ->getFirstItem();

$requestInfo = [
    'qty' => 1,
    'super_attribute' => [
        $attribute->getId() => $option->getId(),
    ],
];

$qtyOrdered = 1;
/** @var Item $orderItem */
$orderConfigurableItem = $objectManager->create(Item::class);
$orderConfigurableItem->setProductId($configurableProduct->getId())->setQtyOrdered($qtyOrdered);
$orderConfigurableItem->setSku($configurableProduct->getSku());
$orderConfigurableItem->setName($configurableProduct->getName());
$orderConfigurableItem->setBasePrice($configurableProduct->getPrice());
$orderConfigurableItem->setPrice($configurableProduct->getPrice());
$orderConfigurableItem->setRowTotal($configurableProduct->getPrice());
$orderConfigurableItem->setParentItemId(null);
$orderConfigurableItem->setProductType('configurable');
$orderConfigurableItem->setProductOptions(['info_buyRequest' => $requestInfo]);
if ($configurableProduct->getExtensionAttributes()
    && (array)$configurableProduct->getExtensionAttributes()->getConfigurableProductLinks()
) {
    $simpleProductId = current($configurableProduct->getExtensionAttributes()->getConfigurableProductLinks());
    /** @var ProductInterface $simpleProduct */
    $simpleProduct = $productRepository->getById($simpleProductId);
    $requestInfo['product'] = $simpleProductId;
    $requestInfo['item'] = $simpleProduct;
    $orderConfigurableItem->setProductOptions(['info_buyRequest' => $requestInfo]);
}

/**
 * virtual product
 */
$virtualProductItem = $objectManager->create(Item::class);
$requestInfo = [
    'qty' => 1
];
$virtualProductItem->setProductId($virtualProduct->getId())
    ->setQtyOrdered(1)
    ->setBasePrice($virtualProduct->getPrice())
    ->setPrice($virtualProduct->getPrice())
    ->setRowTotal($virtualProduct->getPrice())
    ->setProductType($virtualProduct->getTypeId())
    ->setName($virtualProduct->getName())
    ->setSku($virtualProduct->getSku())
    ->setStoreId(0)
    ->setProductOptions(['info_buyRequest' => $requestInfo]);
/**
 * downloadable product
 */
/** @var $linkCollection Collection */
$linkCollection = Bootstrap::getObjectManager()->create(
    Link::class
)->getCollection()->addProductToFilter(
    $downloadableProduct->getId()
)->addTitleToResult(
    $downloadableProduct->getStoreId()
)->addPriceToResult(
    $downloadableProduct->getStore()->getWebsiteId()
);

/** @var $link Link */
$links = $linkCollection->getItems();
$requestInfo = [
    'qty' => 1,
    'links' => array_keys($links)
];

$downloadableProductItem = $objectManager->create(Item::class);
$downloadableProductItem->setProductId($downloadableProduct->getId())
    ->setQtyOrdered(1)
    ->setBasePrice($downloadableProduct->getPrice())
    ->setPrice($downloadableProduct->getPrice())
    ->setRowTotal($downloadableProduct->getPrice())
    ->setProductType($downloadableProduct->getTypeId())
    ->setName($downloadableProduct->getName())
    ->setSku($downloadableProduct->getSku())
    ->setStoreId($downloadableProduct->getStoreId())
    ->setProductOptions(['info_buyRequest' => $requestInfo]);

/**
 * bundle product
 */
/** @var $typeInstance \Magento\Bundle\Model\Product\Type */
$typeInstance = $bundleProduct->getTypeInstance();
$typeInstance->setStoreFilter($bundleProduct->getStoreId(), $bundleProduct);
$optionCollection = $typeInstance->getOptionsCollection($bundleProduct);

$bundleOptions = [];
$bundleOptionsQty = [];
foreach ($optionCollection as $option) {
    /** @var $option Option */
    $selectionsCollection = $typeInstance->getSelectionsCollection([$option->getId()], $bundleProduct);
    if ($option->isMultiSelection()) {
        $bundleOptions[$option->getId()] = array_column($selectionsCollection->toArray(), 'selection_id');
    } else {
        $bundleOptions[$option->getId()] = $selectionsCollection->getFirstItem()->getSelectionId();
    }
    $bundleOptionsQty[$option->getId()] = 1;
}

$requestInfo = [
    'product' => $bundleProduct->getId(),
    'bundle_option' => $bundleOptions,
    'bundle_option_qty' => $bundleOptionsQty,
    'qty' => 1,
];
/** @var Item $orderItem */
$orderBundleItem = $objectManager->create(Item::class);
$orderBundleItem->setProductId($bundleProduct->getId());
$orderBundleItem->setSku($bundleProduct->getSku());
$orderBundleItem->setName($bundleProduct->getName());
$orderBundleItem->setQtyOrdered(1);
$orderBundleItem->setBasePrice($bundleProduct->getPrice());
$orderBundleItem->setPrice($bundleProduct->getPrice());
$orderBundleItem->setRowTotal($bundleProduct->getPrice());
$orderBundleItem->setProductType($bundleProduct->getTypeId());
$orderBundleItem->setProductOptions(['info_buyRequest' => $requestInfo]);

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
$order->addItem($simpleProductItem);
$order->addItem($orderConfigurableItem);
$order->addItem($virtualProductItem);
$order->addItem($orderBundleItem);
$order->addItem($downloadableProductItem);
$order->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId());
$order->setSubtotal(100);
$order->setBaseSubtotal(100);
$order->setBaseGrandTotal(100);
$order->setCustomerId($customerIdFromFixture)->setCustomerIsGuest(false);

$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
$orderRepository->save($order);

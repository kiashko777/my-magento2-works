<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Area;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/address_list.php');

Bootstrap::getInstance()->loadArea(Area::AREA_FRONTEND);

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

/** @var Product $product */
$product = $objectManager->create(Product::class);
$product->setTypeId('simple')
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setName('Simple Products')
    ->setSku('simple-product-guest-quote')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
        ]
    )->save();
$product = $productRepository->get('simple-product-guest-quote');

$addressData = [
    'telephone' => 3234676,
    'postcode' => 47676,
    'country_id' => 'DE',
    'city' => 'CityX',
    'street' => ['Black str, 48'],
    'lastname' => 'Smith',
    'firstname' => 'John',
    'vat_id' => 12345,
    'address_type' => 'shipping',
    'email' => 'some_email@mail.com',
];

$billingAddress = $objectManager->create(
    Address::class,
    ['data' => $addressData]
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$store = $objectManager->get(StoreManagerInterface::class)->getStore();

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setCustomerIsGuest(true)
    ->setStoreId($store->getId())
    ->setReservedOrderId('guest_quote')
    ->setCheckoutMethod('guest')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->addProduct($product);
$quote->getPayment()->setMethod('checkmo');
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate')->setCollectShippingRates(true);
$quote->collectTotals();

$quoteRepository = $objectManager->create(CartRepositoryInterface::class);
$quoteRepository->save($quote);

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager->create(QuoteIdMaskFactory::class)->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();

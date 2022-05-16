<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getInstance()->loadArea(
    FrontNameResolver::AREA_CODE
);

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    'virtual'
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'Simple Products'
)->setSku(
    'simple'
)->setPrice(
    10
)->setStoreId(
    1
)->setStockData(
    ['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 100]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->save();
$product->load(1);

/** @var $quote Quote */
$quote = Bootstrap::getObjectManager()->create(Quote::class);
$quoteItem = $quote->setCustomerId(
    1
)->setStoreId(
    Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getStore()->getId()
)->setReservedOrderId(
    'test01'
)->addProduct(
    $product,
    10
);
/** @var $quoteItem Item */
$quoteItem->setQty(1);
$quote->getPayment()->setMethod('checkmo');
$quote->getBillingAddress();
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->collectTotals();
$quote->save();
$quoteItem->setQuote($quote);
$quoteItem->save();

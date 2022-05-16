<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
$quote = $objectManager->create(Quote::class);
$quote->load('test_order_item_with_items', 'reserved_order_id');
$quoteId = $quote->getId();
if ($quote->getId()) {
    $quote->delete();
}
$product = $objectManager->create(Product::class);
$product->load($product->getIdBySku('simple_one'));
if ($product->getId()) {
    $product->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/quote_with_address_rollback.php');

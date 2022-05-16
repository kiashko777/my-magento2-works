<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

// Delete quote
/** @var $quote Quote */
$quote = $objectManager->create(Quote::class);
$quote->load('test01', 'reserved_order_id');
if ($quote->getId()) {
    $quote->delete();
}
// Delete products
$productSkus = ['simple-1', 'simple-2', 'bundle-product'];
/** @var Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(Magento\Catalog\Api\ProductRepositoryInterface::class);
foreach ($productSkus as $sku) {
    try {
        $product = $productRepository->get($sku, false, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $e) {
        //Products already removed
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

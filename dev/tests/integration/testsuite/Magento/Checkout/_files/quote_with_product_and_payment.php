<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/products.php');

$objectManager = Bootstrap::getObjectManager();
/** @var $quote Quote */
$quote = $objectManager->create(Quote::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
$quote->setStoreId(1)->setIsActive(false)->setIsMultiShipping(false)->addProduct($product, 2);

$quote->getPayment()->setMethod('checkmo');

$quote->collectTotals();
$quote->save();

$quote->getPayment()->setMethod('checkmo');

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\Review;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_xss.php');

$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('product-with-xss');

$review = $objectManager->create(Review::class);
$review->setEntityId(
    $review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)
)->setEntityPkValue(
    $product->getId()
)->setStatusId(
    Review::STATUS_PENDING
)->setStoreId(
    Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getStore()->getId()
)->setStores(
    [
        Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getStore()->getId()
    ]
)->setNickname(
    'Nickname'
)->setTitle(
    'Review Summary'
)->setDetail(
    'Review text'
)->save();

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

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiple_products.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple1');
$review = $objectManager->create(
    Review::class,
    ['data' => ['nickname' => 'Nickname', 'title' => 'Review Summary', 'detail' => 'Review text']]
);
$review->setEntityId(
    $review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)
)->setEntityPkValue(
    $product->getId()
)->setStatusId(
    Review::STATUS_PENDING
)->setStoreId(
    $objectManager->get(
        StoreManagerInterface::class
    )->getStore()->getId()
)->setStores(
    [
        $objectManager->get(
            StoreManagerInterface::class
        )->getStore()->getId()
    ]
)->save();

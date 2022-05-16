<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Registry;
use Magento\Review\Model\Review;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

Bootstrap::getInstance()->loadArea(
    FrontNameResolver::AREA_CODE
);
$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = $objectManager->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
$review = $objectManager->create(
    Review::class,
    ['data' => [
        'customer_id' => $customer->getId(),
        'title' => 'Review Summary',
        'detail' => 'Review text',
        'nickname' => 'Nickname',
    ]]
);

$review
    ->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
    ->setEntityPkValue($product->getId())
    ->setStatusId(Review::STATUS_PENDING)
    ->setStoreId(
        $objectManager->get(
            StoreManagerInterface::class
        )->getStore()->getId()
    )
    ->setStores([
        $objectManager->get(
            StoreManagerInterface::class
        )->getStore()->getId()
    ])
    ->save();

$objectManager->get(Registry::class)->register(
    'review_data',
    $review
);

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManager $objectManager */

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Model\CategoryLinkRepository;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();

$objectManager->removeSharedInstance(ProductRepository::class);
$objectManager->removeSharedInstance(CategoryLinkRepository::class);

/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = $objectManager->create(CategoryLinkManagementInterface::class);

$product = $objectManager->create(Product::class);

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('<script>alert("xss");</script>')
    ->setSku('product-with-xss')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->save();

$categoryLinkManagement->assignProductToCategories(
    $product->getSku(),
    [2]
);

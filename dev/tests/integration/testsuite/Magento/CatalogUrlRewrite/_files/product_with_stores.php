<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/CatalogUrlRewrite/_files/categories_with_stores.php');

/** @var CategorySetup $installer */
$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);

$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$categoryLinkRepository = $objectManager->create(
    CategoryLinkRepositoryInterface::class,
    [
        'productRepository' => $productRepository
    ]
);

/** @var Magento\Catalog\Api\CategoryLinkManagementInterface $linkManagement */
$categoryLinkManagement = $objectManager->create(
    CategoryLinkManagementInterface::class,
    [
        'productRepository' => $productRepository,
        'categoryLinkRepository' => $categoryLinkRepository
    ]
);

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setWeight(18)
    ->setStockData(['use_config_manage_stock' => 0])
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->save();
$categoryLinkManagement->assignProductToCategories($product->getSku(), [4]);

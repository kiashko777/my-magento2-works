<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductExtensionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

/** @var ProductRepositoryInterface $productRepository */
// simple product without custom options
$secondProductSku = 'simple-2';

/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();

$productExtensionAttributesFactory = $objectManager->get(ProductExtensionInterfaceFactory::class);

$adminWebsite = $objectManager->get(WebsiteRepositoryInterface::class)->get('admin');
/** @var  $tpExtensionAttributes */
$tpExtensionAttributesFactory = $objectManager->get(ProductTierPriceExtensionFactory::class);
$tierPriceExtensionAttributes1 = $tpExtensionAttributesFactory->create()
    ->setWebsiteId($adminWebsite->getId());
$productExtensionAttributesWebsiteIds = $productExtensionAttributesFactory->create(
    ['website_ids' => $adminWebsite->getId()]
);
/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = $objectManager->get(CategoryLinkManagementInterface::class);
/** @var $secondProduct Product */
$secondProduct = $objectManager->create(Product::class);
$secondProduct->isObjectNew(true);
$secondProduct->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(22)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 2')
    ->setSku($secondProductSku)
    ->setPrice(11)
    ->setWeight(1)
    ->setShortDescription("Short description 2")
    ->setTaxClassId(0)
    ->setDescription('Description with <b>html tag</b>')
    ->setExtensionAttributes($productExtensionAttributesWebsiteIds)
    ->setMetaTitle('meta title 2')
    ->setMetaKeyword('meta keyword 2')
    ->setMetaDescription('meta description 2')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    )->setCanSaveCustomOptions(false)
    ->setHasOptions(false);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$productRepository->save($secondProduct);

$categoryLinkManagement->assignProductToCategories(
    $secondProduct->getSku(),
    [2]
);

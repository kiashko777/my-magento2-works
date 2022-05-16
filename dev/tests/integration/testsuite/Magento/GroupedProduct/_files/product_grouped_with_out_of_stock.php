<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_associated.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_virtual_in_stock.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_virtual_out_of_stock.php');

$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(
    Grouped::TYPE_CODE
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Grouped Products'
)->setSku(
    'grouped-product'
)->setPrice(
    100
)->setTaxClassId(
    0
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
);

$newLinks = [];
$productLinkFactory = $objectManager->get(ProductLinkInterfaceFactory::class);

/** @var ProductLinkInterface $productLink */
$productLink = $productLinkFactory->create();
$linkedProduct = $productRepository->getById(1);
$productLink->setSku($product->getSku())
    ->setLinkType('associated')
    ->setLinkedProductSku($linkedProduct->getSku())
    ->setLinkedProductType($linkedProduct->getTypeId())
    ->setPosition(1)
    ->getExtensionAttributes()
    ->setQty(1);
$newLinks[] = $productLink;

$subProductsSkus = ['virtual-product', 'virtual-product-out'];
foreach ($subProductsSkus as $sku) {
    /** @var ProductLinkInterface $productLink */
    $productLink = $productLinkFactory->create();
    $linkedProduct = $productRepository->get($sku);
    $productLink->setSku($product->getSku())
        ->setLinkType('associated')
        ->setLinkedProductSku($sku)
        ->setLinkedProductType($linkedProduct->getTypeId())
        ->getExtensionAttributes()
        ->setQty(2.5);
    $newLinks[] = $productLink;
}
$product->setProductLinks($newLinks);
$product->save();

/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = Bootstrap::getObjectManager()
    ->create(CategoryLinkManagementInterface::class);

$categoryLinkManagement->assignProductToCategories(
    $product->getSku(),
    [2]
);

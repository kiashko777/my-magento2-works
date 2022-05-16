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
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture(
    'Magento/Catalog/_files/product_associated.php'
);
Resolver::getInstance()->requireDataFixture(
    'Magento/Catalog/_files/product_virtual_in_stock.php'
);

$objectManager = Bootstrap::getObjectManager();


/** @var WebsiteRepositoryInterface $repository */
$repository = $objectManager->get(WebsiteRepositoryInterface::class);
$websiteId = $repository->get('test')->getId();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(
    Grouped::TYPE_CODE
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1, $websiteId]
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
$linkedProduct->setWebsiteIds([1])->save();

$productLink->setSku($product->getSku())
    ->setLinkType('associated')
    ->setLinkedProductSku($linkedProduct->getSku())
    ->setLinkedProductType($linkedProduct->getTypeId())
    ->setPosition(1)
    ->getExtensionAttributes()
    ->setQty(1);
$newLinks[] = $productLink;

/** @var ProductLinkInterface $productLink */
$productLink = $productLinkFactory->create();
$linkedProduct = $productRepository->getById(21);
$linkedProduct->setWebsiteIds([$websiteId])->save();
$productLink->setSku($product->getSku())
    ->setLinkType('associated')
    ->setLinkedProductSku($linkedProduct->getSku())
    ->setLinkedProductType($linkedProduct->getTypeId())
    ->setPosition(2)
    ->getExtensionAttributes()
    ->setQty(2);
$newLinks[] = $productLink;
$product->setProductLinks($newLinks);
$product->setStockData(['use_config_manage_stock' => 1, 'is_in_stock' => 1]);
$productRepository->save($product);

/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = Bootstrap::getObjectManager()
    ->create(CategoryLinkManagementInterface::class);

$categoryLinkManagement->assignProductToCategories(
    $product->getSku(),
    [2]
);

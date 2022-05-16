<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getInstance()
    ->loadArea(FrontNameResolver::AREA_CODE);

/** @var $installer CategorySetup */
$objectManager = Bootstrap::getObjectManager();
$installer = $objectManager->create(CategorySetup::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

$skus = ['product1', 'product2'];
foreach ($skus as $sku) {
    /** @var $product Product */
    $product = $objectManager->create(Product::class);
    $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
        ->setStoreId(0)
        ->setWebsiteIds([1])
        ->setName('Product1')
        ->setSku($sku)
        ->setPrice(10)
        ->setWeight(18)
        ->setStockData(['use_config_manage_stock' => 0])
        ->setUrlKey('product-1')
        ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
        ->setStatus(Status::STATUS_ENABLED);
    $productRepository->save($product);
}

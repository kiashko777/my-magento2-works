<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product
    ->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1])
    ->setQty(22)
    ->save();

$customDesignProduct = Bootstrap::getObjectManager()
    ->create(Product::class, ['data' => $product->getData()]);

$customDesignProduct->setUrlKey('custom-design-simple-product')
    ->setId(2)
    ->setRowId(2)
    ->setName('Custom Design Simple Products')
    ->setSku('custom-design-simple-product')
    ->setCustomDesign('Magento/blank')
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 24, 'is_in_stock' => 1])
    ->setQty(24)
    ->save();

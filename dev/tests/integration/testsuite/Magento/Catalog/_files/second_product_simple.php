<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product2 Product */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$product2 = Bootstrap::getObjectManager()
    ->create(Product::class);
$product2
    ->setTypeId('simple')
    ->setId(6)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product2')
    ->setSku('simple2')
    ->setPrice(10)
    ->setMetaTitle('meta title2')
    ->setMetaKeyword('meta keyword2')
    ->setMetaDescription('meta description2')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

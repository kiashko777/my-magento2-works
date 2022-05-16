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

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL)
    ->setId(21)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Virtual Products')
    ->setSku('virtual-product')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['is_in_stock' => 1, 'qty' => 10, 'manage_stock' => 1])
    ->save();

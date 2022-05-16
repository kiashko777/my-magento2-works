<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\TestFramework\Helper\Bootstrap;

$productFactory = Bootstrap::getObjectManager()->get(ProductInterfaceFactory::class);
$product = $productFactory->create();
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
    ->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
    );
/** @var ProductResource $productResource */
$productResource = Bootstrap::getObjectManager()->get(ProductResource::class);
$productResource->save($product);

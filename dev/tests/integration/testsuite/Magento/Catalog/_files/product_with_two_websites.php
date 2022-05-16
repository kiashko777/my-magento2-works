<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Magento\Store\Model\Website $website */
$website = $objectManager->get(Magento\Store\Model\Website::class);

$website->setData(
    [
        'code' => 'second_website',
        'name' => 'Test Website',
    ]
);

$website->save();

$objectManager->get(StoreManagerInterface::class)->reinitStores();

/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(ProductInterface::class);
$product
    ->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1, $website->getId()])
    ->setName('Simple Products')
    ->setSku('unique-simple-azaza')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

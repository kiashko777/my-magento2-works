<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ProductFactory $factory */
$factory = Bootstrap::getObjectManager()->get(ProductFactory::class);
/** @var ProductLinkInterfaceFactory $linkFactory */
$linkFactory = Bootstrap::getObjectManager()->get(ProductLinkInterfaceFactory::class);

$rootProductCount = 10;
$rootSku = 'simple-related-';
$simpleProducts = [];
for ($i = 1; $i <= $rootProductCount; $i++) {
    /** @var Product $product */
    $product = $factory->create();
    $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        ->setAttributeSetId(4)
        ->setName('Simple Related Products #' . $i)
        ->setSku($rootSku . $i)
        ->setPrice(10)
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_ENABLED)
        ->setWebsiteIds([1])
        ->setStockData(['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1])
        ->save();
    $simpleProducts[$i] = $product;
}

$linkTypes = ['crosssell', 'related', 'upsell'];
$linkedMaxCount = 10;
foreach ($simpleProducts as $simpleI => $product) {
    $linkedCount = rand(1, $linkedMaxCount);
    $links = [];
    for ($i = 0; $i < $linkedCount; $i++) {
        /** @var Product $linkedProduct */
        $linkedProduct = $factory->create();
        $linkedSku = 'related-product-' . $simpleI . '-' . $i;
        $linkedProduct->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ->setAttributeSetId(4)
            ->setName('Related product #' . $simpleI . '-' . $i)
            ->setSku($linkedSku)
            ->setPrice(10)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED)
            ->setWebsiteIds([1])
            ->setStockData(['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1])
            ->save();
        /** @var ProductLinkInterface $link */
        $link = $linkFactory->create();
        $link->setSku($product->getSku());
        $link->setLinkedProductSku($linkedSku);
        $link->setPosition($i + 1);
        $link->setLinkType($linkTypes[rand(0, count($linkTypes) - 1)]);
        $links[] = $link;
    }
    $product->setProductLinks($links);
    $product->save();
}

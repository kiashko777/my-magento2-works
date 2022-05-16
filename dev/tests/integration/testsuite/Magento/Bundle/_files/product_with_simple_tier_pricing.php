<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

$objectManager = Bootstrap::getObjectManager();
/** @var $productFactory Magento\Catalog\Model\ProductFactory */
$productFactory = $objectManager->create(ProductFactory::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
/** @var $bundleProduct Product */
$bundleProduct = $productFactory->create();
$bundleProduct->setTypeId('bundle')
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setPriceType(Price::PRICE_TYPE_DYNAMIC)
    ->setPriceView(1)
    ->setName('Bundle Products')
    ->setSku('bundle-product')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData([
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ])
    ->setBundleOptionsData(
        [
            [
                'title' => 'Bundle Products Items',
                'default_title' => 'Bundle Products Items',
                'type' => 'checkbox',
                'required' => 1,
                'delete' => '',
            ],
        ]
    )
    ->setBundleSelectionsData(
        [[['product_id' => $product->getId(), 'selection_qty' => 1, 'delete' => '']]]
    );
$productRepository->save($bundleProduct);

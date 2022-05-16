<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var ObjectManager $objectManager */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Model\Group;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->isObjectNew(true);

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(100)
    ->setWeight(1)
    ->setTierPrice([0 => ['website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => 95]])
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCanSaveCustomOptions(true)
    ->setStockData(
        [
            'qty' => 10,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
    );
$productRepository->save($product);
$product->unsetData()->setOrigData();

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Second Simple Products')
    ->setSku('second_simple')
    ->setPrice(200)
    ->setWeight(1)
    ->setTierPrice(
        [
            0 => [
                'website_id' => 0,
                'cust_group' => Group::CUST_GROUP_ALL,
                'price_qty' => 10,
                'price' => 3,
                'percentage_value' => 3,
            ],
        ]
    )
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCanSaveCustomOptions(true)
    ->setStockData(
        [
            'qty' => 10,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
    );

$productRepository->save($product);

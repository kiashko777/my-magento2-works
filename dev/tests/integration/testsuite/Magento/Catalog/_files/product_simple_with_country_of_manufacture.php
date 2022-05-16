<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

Bootstrap::getInstance()->reinitialize();

/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
/** @var ProductRepositoryInterface $productRepository */
$productFactory = $objectManager->create(ProductInterfaceFactory::class);
/** @var $product Product */

$product = $productFactory->create();
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Simple Products With Country Of Manufacture')
    ->setSku('simple_with_com')
    ->setPrice(10)
    ->setWeight(1)
    ->setCountryOfManufacture('AO')
    ->setShortDescription('Short description')
    ->setTaxClassId(0)
    ->setDescription('Description')
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    );

$productRepository->save($product);

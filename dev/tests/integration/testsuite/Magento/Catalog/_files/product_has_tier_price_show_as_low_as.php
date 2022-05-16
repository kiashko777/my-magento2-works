<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Model\Group;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

Bootstrap::getInstance()->reinitialize();

/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();

$tierPrices = [];
/** @var ProductTierPriceInterfaceFactory $tierPriceFactory */
$tierPriceFactory = $objectManager->get(ProductTierPriceInterfaceFactory::class);
$tierPrices[] = $tierPriceFactory->create(
    [
        'data' => [
            'customer_group_id' => Group::CUST_GROUP_ALL,
            'qty' => 2,
            'value' => 8
        ]
    ]
);
$tierPrices[] = $tierPriceFactory->create(
    [
        'data' => [
            'customer_group_id' => Group::CUST_GROUP_ALL,
            'qty' => 5,
            'value' => 5
        ]
    ]
);
$tierPrices[] = $tierPriceFactory->create(
    [
        'data' => [
            'customer_group_id' => Group::NOT_LOGGED_IN_ID,
            'qty' => 3,
            'value' => 5
        ]
    ]
);

/** @var  $tpExtensionAttributes */
$tpExtensionAttributesFactory = $objectManager->create(ProductTierPriceExtensionFactory::class);
$tpExtensionAttributes = $tpExtensionAttributesFactory->create()->setPercentageValue(50);

$tierPrices[] = $tierPriceFactory->create(
    [
        'data' => [
            'customer_group_id' => Group::NOT_LOGGED_IN_ID,
            'qty' => 10
        ]
    ]
)->setExtensionAttributes($tpExtensionAttributes);

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('tier_prices')
    ->setPrice(10)
    ->setWeight(1)
    ->setShortDescription("Short description")
    ->setTaxClassId(0)
    ->setTierPrices($tierPrices)
    ->setDescription('Description with <b>html tag</b>')
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

/** @var ProductRepositoryInterface $productRepositoryFactory */
$productRepositoryFactory = $objectManager->create(ProductRepositoryInterface::class);
$productRepositoryFactory->save($product);

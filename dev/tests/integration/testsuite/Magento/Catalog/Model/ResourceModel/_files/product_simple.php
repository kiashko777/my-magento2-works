<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Model\Group;
use Magento\TestFramework\Helper\Bootstrap;

/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('simple products')
    ->setPrice(10)
    ->setWeight(1)
    ->setShortDescription("Short description")
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => Group::NOT_LOGGED_IN_ID,
                'price_qty' => 2,
                'price' => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => Group::CUST_GROUP_ALL,
                'price_qty' => 21,
                'price' => 81,
            ],
            [
                'website_id' => 0,
                'cust_group' => Group::CUST_GROUP_ALL,
                'price_qty' => 30,
                'percentage_value' => 50,
            ],
        ]
    )
    ->setDescription('Description with <b>html tag</b>')
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setUrlKey('url-key')
    ->setUrlPath('url-key')
    ->save();

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ProductCustomOptionInterfaceFactory $customOptionFactory */

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Model\GroupManagement;
use Magento\TestFramework\Helper\Bootstrap;

$customOptionFactory = Bootstrap::getObjectManager()
    ->create(ProductCustomOptionInterfaceFactory::class);
/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Unisex green socks')
    ->setSku('green_socks')
    ->setPrice(12)
    ->setWeight(1)
    ->setShortDescription("Unisex green socks for some good peoples at one")
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ],
        ]
    )
    ->setDescription('Unisex <b>green socks</b> for some good peoples at one')
    ->setMetaTitle('green socks metadata')
    ->setMetaKeyword('green,socks,unisex')
    ->setMetaDescription('green socks metadata description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    );
$option = [
    'previous_group' => 'text',
    'title' => 'Stone',
    'type' => 'field',
    'is_require' => 1,
    'sort_order' => 0,
    'price' => 1,
    'price_type' => 'fixed',
    'sku' => 'stone-1',
    'max_characters' => 100,
];
/** @var ProductCustomOptionInterface $customOption */
$customOption = $customOptionFactory->create(['data' => $option]);
$customOption->setProductSku($product->getSku());
$product->setCanSaveCustomOptions(true)
    ->setOptions([$customOption])
    ->setHasOptions(true)
    ->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(2)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('White shorts')
    ->setSku('white_shorts')
    ->setPrice(14)
    ->setWeight(2)
    ->setShortDescription("Small white shorts for your children")
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ],
        ]
    )
    ->setDescription('Small <b>white shorts</b> for your children')
    ->setMetaTitle('white shorts for your children metadata')
    ->setMetaKeyword('white,shorts,children')
    ->setMetaDescription('white shorts for your children metadata description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    );
$option = [
    'previous_group' => 'text',
    'title' => 'Gold',
    'type' => 'field',
    'is_require' => 1,
    'sort_order' => 0,
    'price' => 1,
    'price_type' => 'fixed',
    'sku' => 'Gold',
    'max_characters' => 100,
];
/** @var ProductCustomOptionInterface $customOption */
$customOption = $customOptionFactory->create(['data' => $option]);
$customOption->setProductSku($product->getSku());
$product->setCanSaveCustomOptions(true)
    ->setOptions([$customOption])
    ->setHasOptions(true)
    ->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Red trousers')
    ->setSku('red_trousers')
    ->setPrice(16)
    ->setWeight(3)
    ->setShortDescription("Red pants for men")
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ],
        ]
    )
    ->setDescription('Red pants for <b>men</b>')
    ->setMetaTitle('Red trousers meta title')
    ->setMetaKeyword('red,trousers,meta,men')
    ->setMetaDescription('Red trousers meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    );
$option = [
    'previous_group' => 'text',
    'title' => 'Silver',
    'type' => 'field',
    'is_require' => 1,
    'sort_order' => 0,
    'price' => 1,
    'price_type' => 'fixed',
    'sku' => 'silver',
    'max_characters' => 100,
];
/** @var ProductCustomOptionInterface $customOption */
$customOption = $customOptionFactory->create(['data' => $option]);
$customOption->setProductSku($product->getSku());
$product->setCanSaveCustomOptions(true)
    ->setOptions([$customOption])
    ->setHasOptions(true)
    ->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(4)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Blue briefs')
    ->setSku('blue_briefs')
    ->setPrice(18)
    ->setWeight(3)
    ->setShortDescription("Blue briefs for Russian men")
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ],
        ]
    )
    ->setDescription('Blue briefs for <b>men</b>')
    ->setMetaTitle('Blue briefs meta title')
    ->setMetaKeyword('blue,briefs,meta,men')
    ->setMetaDescription('Blue briefs meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    )
    ->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(5)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Grey shorts')
    ->setSku('grey_shorts')
    ->setPrice(20)
    ->setWeight(3)
    ->setShortDescription("Grey or green shorts for all peoples at one")
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ],
            [
                'website_id' => 0,
                'cust_group' => GroupManagement::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ],
        ]
    )
    ->setDescription('Grey or green shorts for peoples at <b>one</b>')
    ->setMetaTitle('Grey shorts meta title')
    ->setMetaKeyword('grey,shorts,meta,men')
    ->setMetaDescription('Grey shorts meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    )
    ->save();

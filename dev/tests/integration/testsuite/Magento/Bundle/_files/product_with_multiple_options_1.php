<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Bundle\Api\Data\LinkInterfaceFactory;
use Magento\Bundle\Api\Data\OptionInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiple_products.php');

$objectManager = Bootstrap::getObjectManager();

$productIds = range(10, 12, 1);
foreach ($productIds as $productId) {
    /** @var Item $stockItem */
    $stockItem = $objectManager->create(Item::class);
    $stockItem->load($productId, 'product_id');

    if (!$stockItem->getProductId()) {
        $stockItem->setProductId($productId);
    }
    $stockItem->setUseConfigManageStock(1);
    $stockItem->setQty(1000);
    $stockItem->setIsQtyDecimal(0);
    $stockItem->setIsInStock(1);
    $stockItem->save();
}

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Bundle Products')
    ->setSku('bundle-product')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setPriceView(1)
    ->setSkuType(1)
    ->setWeightType(1)
    ->setPriceType(1)
    ->setPrice(10.0)
    ->setShipmentType(0)
    ->setBundleOptionsData(
        [
            // Required "Drop-down" option
            [
                'title' => 'Option 1',
                'default_title' => 'Option 1',
                'type' => 'select',
                'required' => 1,
                'delete' => '',
            ],

        ]
    )->setBundleSelectionsData(
        [
            [
                [
                    'product_id' => 10,
                    'selection_qty' => 1,
                    'selection_price_value' => 2.75,
                    'selection_can_change_qty' => 1,
                    'delete' => '',
                    'option_id' => 1
                ],
                [
                    'product_id' => 11,
                    'selection_qty' => 1,
                    'selection_price_value' => 6.75,
                    'selection_can_change_qty' => 1,
                    'delete' => '',
                    'option_id' => 1
                ]
            ]

        ]
    );
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

if ($product->getBundleOptionsData()) {
    $options = [];
    foreach ($product->getBundleOptionsData() as $key => $optionData) {
        if (!(bool)$optionData['delete']) {
            $option = $objectManager->create(OptionInterfaceFactory::class)
                ->create(['data' => $optionData]);
            $option->setSku($product->getSku());
            $option->setOptionId(null);

            $links = [];
            $bundleLinks = $product->getBundleSelectionsData();
            if (!empty($bundleLinks[$key])) {
                foreach ($bundleLinks[$key] as $linkData) {
                    if (!(bool)$linkData['delete']) {
                        $link = $objectManager->create(LinkInterfaceFactory::class)
                            ->create(['data' => $linkData]);
                        $linkProduct = $productRepository->getById($linkData['product_id']);
                        $link->setSku($linkProduct->getSku());
                        $link->setQty($linkData['selection_qty']);
                        $link->setPrice($linkData['selection_price_value']);
                        $links[] = $link;
                    }
                }
                $option->setProductLinks($links);
                $options[] = $option;
            }
        }
    }
    $extension = $product->getExtensionAttributes();
    $extension->setBundleProductOptions($options);
    $product->setExtensionAttributes($extension);
}
$product->save();

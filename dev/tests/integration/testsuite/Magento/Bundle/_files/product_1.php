<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Bundle\Api\Data\LinkInterface;
use Magento\Bundle\Api\Data\LinkInterfaceFactory;
use Magento\Bundle\Api\Data\OptionInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/*
 * Since the bundle product creation GUI doesn't allow to choose values for bundled products' custom options,
 * bundled items should not contain products with required custom options.
 * However, if to create such a bundle product, it will be always out of stock.
 */
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/products.php');

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$sampleProduct = $productRepository->get('simple');

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId('bundle')
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWeight(2)
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
    ->setShipmentType(0)
    ->setPrice(10.0)
    ->setBundleOptionsData(
        [
            [
                'title' => 'Bundle Products Items',
                'default_title' => 'Bundle Products Items',
                'type' => 'select', 'required' => 1,
                'delete' => '',
            ],
        ]
    )
    ->setBundleSelectionsData(
        [
            [
                [
                    'product_id' => $sampleProduct->getId(),
                    'selection_price_value' => 2.75,
                    'selection_qty' => 1,
                    'selection_can_change_qty' => 1,
                    'delete' => '',

                ],
            ],
        ]
    );

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
                        /** @var LinkInterface $link */
                        $link = $objectManager->create(LinkInterfaceFactory::class)
                            ->create(['data' => $linkData]);
                        $linkProduct = $productRepository->getById($linkData['product_id']);
                        $link->setSku($linkProduct->getSku());
                        $link->setQty($linkData['selection_qty']);
                        $link->setPrice($linkData['selection_price_value']);
                        if (isset($linkData['selection_can_change_qty'])) {
                            $link->setCanChangeQuantity($linkData['selection_can_change_qty']);
                        }
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

$productRepository->save($product, true);

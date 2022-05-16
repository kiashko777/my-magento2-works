<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/configurable_attribute.php');
Resolver::getInstance()->requireDataFixture('Magento/CatalogRule/_files/simple_products.php');

$objectManager = Bootstrap::getObjectManager();

$storeManager = $objectManager->get(StoreManager::class);
$store = $storeManager->getStore('default');
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

$installer = $objectManager->get(CategorySetup::class);
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$attributeValues = [];
$associatedProductIds = [];

$attributeRepository = $objectManager->get(AttributeRepositoryInterface::class);
$attribute = $attributeRepository->get('catalog_product', 'test_configurable');
$options = $attribute->getOptions();
array_shift($options); //remove the first option which is empty
foreach (['simple1', 'simple2'] as $sku) {
    $option = array_shift($options);
    $product = $productRepository->get($sku);
    $product->setTestConfigurable($option->getValue());
    $productRepository->save($product);
    $attributeValues[] = [
        'label' => 'test',
        'attribute_id' => $attribute->getId(),
        'value_index' => $option->getValue(),
    ];
    $associatedProductIds[] = $product->getId();
}

$product = $objectManager->create(Product::class)
    ->setTypeId('configurable')
    ->setId(666)
    ->setAttributeSetId($attributeSetId)
    ->setWebsiteIds([1])
    ->setName('Configurable Products')
    ->setSku('configurable')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData([
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ]);
$configurableAttributesData = [
    [
        'attribute_id' => $attribute->getId(),
        'code' => $attribute->getAttributeCode(),
        'label' => $attribute->getStoreLabel(),
        'position' => '0',
        'values' => $attributeValues,
    ],
];
$optionsFactory = $objectManager->get(Factory::class);
$configurableOptions = $optionsFactory->create($configurableAttributesData);
$extensionConfigurableAttributes = $product->getExtensionAttributes();
$extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
$extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);
$product->setExtensionAttributes($extensionConfigurableAttributes);
$productRepository->save($product);

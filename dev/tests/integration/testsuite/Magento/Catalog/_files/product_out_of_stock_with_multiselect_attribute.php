<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/**
 * Create multiselect attribute
 */
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiselect_attribute.php');

/** Create product with options out of stock and multiselect attribute */
$objectManager = Bootstrap::getObjectManager();
/** @var $installer CategorySetup */
$installer = $objectManager->create(
    CategorySetup::class
);
/** @var Config $eavConfig */
$eavConfig = $objectManager->get(Config::class);
$attribute = $eavConfig->getAttribute(Product::ENTITY, 'multiselect_attribute');
/** @var $options Collection */
$options = $objectManager->create(
    Collection::class
);
$options->setAttributeFilter($attribute->getId());
$optionIds = $options->getAllIds();

$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId($optionIds[1] * 20)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('Out of Stock With Multiselect')
    ->setSku('simple_ms_out_of_stock')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setMultiselectAttribute([$optionIds[1], $optionIds[2], $optionIds[3]])
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 0, 'is_in_stock' => 0])
    ->save();

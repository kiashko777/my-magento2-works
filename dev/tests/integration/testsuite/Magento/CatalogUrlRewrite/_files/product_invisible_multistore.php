<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Bootstrap::getInstance()
    ->loadArea(FrontNameResolver::AREA_CODE);

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/store.php');
/** @var $installer CategorySetup */
$objectManager = Bootstrap::getObjectManager();
$installer = $objectManager->create(CategorySetup::class);
$storeManager = $objectManager->get(StoreManagerInterface::class);
$storeManager->setCurrentStore(0);

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setStoreId(0)
    ->setWebsiteIds([1])
    ->setName('Product1')
    ->setSku('product1')
    ->setPrice(10)
    ->setWeight(18)
    ->setStockData(['use_config_manage_stock' => 0])
    ->setUrlKey('product-1')
    ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
    ->setStatus(Status::STATUS_ENABLED);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$productRepository->save($product);

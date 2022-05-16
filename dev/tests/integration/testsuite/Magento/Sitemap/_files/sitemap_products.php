<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// Copy images to tmp media path
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Config $config */
$config = $objectManager->get(Config::class);

/** @var Filesystem $filesystem */
$filesystem = $objectManager->get(Filesystem::class);
/** @var WriteInterface $mediaDirectory */
$mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
$mediaPath = $mediaDirectory->getAbsolutePath();
$baseTmpMediaPath = $config->getBaseTmpMediaPath();
$mediaDirectory->create($baseTmpMediaPath);

copy(__DIR__ . '/magento_image_sitemap.png', $mediaPath . '/' . $baseTmpMediaPath . '/magento_image_sitemap.png');
copy(__DIR__ . '/second_image.png', $mediaPath . '/' . $baseTmpMediaPath . '/second_image.png');

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'Simple Products Enabled'
)->setSku(
    'simple_no_images'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1]
)->save();

/** @var ProductLinkInterface $productLink */
$productLink = $objectManager->create(ProductLinkInterface::class);
$productLink->setSku('simple_invisible');
$productLink->setLinkedProductSku('simple_no_images');
$productLink->setPosition(1);
$productLink->setLinkType('related');

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    2
)->setAttributeSetId(
    4
)->setName(
    'Simple Products Invisible'
)->setSku(
    'simple_invisible'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_NOT_VISIBLE
)->setStatus(
    Status::STATUS_ENABLED
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1]
)->setRelatedLinkData(
    [$productLink]
)->save();

/** @var ProductLinkInterface $productLink */
$productLink = $objectManager->create(ProductLinkInterface::class);
$productLink->setSku('simple_disabled');
$productLink->setLinkedProductSku('simple_no_images');
$productLink->setPosition(1);
$productLink->setLinkType('related');

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    3
)->setAttributeSetId(
    4
)->setName(
    'Simple Products Disabled'
)->setSku(
    'simple_disabled'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_DISABLED
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1]
)->setRelatedLinkData(
    [$productLink]
)->save();

/** @var ProductLinkInterface $productLink */
$productLink = $objectManager->create(ProductLinkInterface::class);
$productLink->setSku('simple_with_images');
$productLink->setLinkedProductSku('simple_no_images');
$productLink->setPosition(1);
$productLink->setLinkType('related');

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    4
)->setAttributeSetId(
    4
)->setName(
    'Simple Images'
)->setSku(
    'simple_with_images'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setImage(
    '/s/e/second_image.png'
)->setSmallImage(
    '/m/a/magento_image_sitemap.png'
)->setThumbnail(
    '/m/a/magento_image_sitemap.png'
)->addImageToMediaGallery(
    $mediaPath . '/' . $baseTmpMediaPath . '/magento_image_sitemap.png',
    null,
    false,
    false
)->addImageToMediaGallery(
    $mediaPath . '/' . $baseTmpMediaPath . '/second_image.png',
    null,
    false,
    false
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1]
)->setRelatedLinkData(
    [$productLink]
)->save();

/** @var ProductLinkInterface $productLink */
$productLink = $objectManager->create(ProductLinkInterface::class);
$productLink->setSku('simple_with_images');
$productLink->setLinkedProductSku('simple_no_images');
$productLink->setPosition(1);
$productLink->setLinkType('related');

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    5
)->setAttributeSetId(
    4
)->setName(
    'Simple Images Two'
)->setSku(
    'simple_with_images'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setImage(
    'no_selection'
)->setSmallImage(
    '/m/a/magento_image_sitemap.png'
)->setThumbnail(
    'no_selection'
)->addImageToMediaGallery(
    $baseTmpMediaPath . '/second_image.png',
    null,
    false,
    false
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1]
)->setRelatedLinkData(
    [$productLink]
)->save();

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// Copy images to tmp media path

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$objectManager->get(
    DesignInterface::class
)->setArea(
    'frontend'
)->setDefaultDesignTheme();

/** @var Config $config */
$config = $objectManager->get(Config::class);
/** @var WriteInterface $mediaDirectory */
$mediaDirectory = $objectManager->get(Filesystem::class)
    ->getDirectoryWrite(DirectoryList::MEDIA);

$baseTmpMediaPath = $config->getBaseTmpMediaPath();
$mediaDirectory->create($baseTmpMediaPath);
$mediaDirectory->getDriver()->filePutContents($mediaDirectory->getAbsolutePath($baseTmpMediaPath . '/product_image.png'), file_get_contents(__DIR__ . '/product_image.png'));

/** @var $productOne Product */
$productOne = $objectManager->create(Product::class);
$productOne->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [$objectManager->get(StoreManagerInterface::class)->getStore()->getWebsiteId()]
)->setSku(
    'simple_product_1'
)->setName(
    'Simple Products 1 Name'
)->setDescription(
    'Simple Products 1 Full Description'
)->setShortDescription(
    'Simple Products 1 Short Description'
)->setPrice(
    1234.56
)->setTaxClassId(
    2
)->setStockData(
    [
        'use_config_manage_stock' => 1,
        'qty' => 99,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ]
)->setMetaTitle(
    'Simple Products 1 Meta Title'
)->setMetaKeyword(
    'Simple Products 1 Meta Keyword'
)->setMetaDescription(
    'Simple Products 1 Meta Description'
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->addImageToMediaGallery(
    $baseTmpMediaPath . '/product_image.png',
    null,
    false,
    false
)->save();

/** @var $productTwo Product */
$productTwo = $objectManager->create(Product::class);
$productTwo->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [$objectManager->get(StoreManagerInterface::class)->getStore()->getWebsiteId()]
)->setSku(
    'simple_product_2'
)->setName(
    'Simple Products 2 Name'
)->setDescription(
    'Simple Products 2 Full Description'
)->setShortDescription(
    'Simple Products 2 Short Description'
)->setPrice(
    987.65
)->setTaxClassId(
    2
)->setStockData(
    ['use_config_manage_stock' => 1, 'qty' => 24, 'is_in_stock' => 1]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->save();

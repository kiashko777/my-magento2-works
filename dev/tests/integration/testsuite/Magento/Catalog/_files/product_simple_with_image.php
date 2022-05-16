<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

Bootstrap::getInstance()->reinitialize();

/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setStatus(Status::STATUS_ENABLED);

/** @var $mediaConfig Config */
$mediaConfig = $objectManager->get(Config::class);

/** @var $mediaDirectory WriteInterface */
$mediaDirectory = $objectManager->get(Filesystem::class)
    ->getDirectoryWrite(DirectoryList::MEDIA);

$targetDirPath = $mediaConfig->getBaseMediaPath();
$targetTmpDirPath = $mediaConfig->getBaseTmpMediaPath();

$mediaDirectory->create($targetDirPath);
$mediaDirectory->create($targetTmpDirPath);

$dist = $mediaDirectory->getAbsolutePath($mediaConfig->getBaseMediaPath() . DIRECTORY_SEPARATOR . 'magento_image.jpg');
$mediaDirectory->getDriver()->filePutContents($dist, file_get_contents(__DIR__ . '/magento_image.jpg'));

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$productRepository->save($product);

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Filesystem $filesystem */
$filesystem = $objectManager->create(Filesystem::class);

/** @var $tmpDirectory WriteInterface */
$tmpDirectory = $filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
$tmpDirectory->create($tmpDirectory->getAbsolutePath());

$targetTmpFilePath = $tmpDirectory->getAbsolutePath('magento_small_image.jpg');
copy(__DIR__ . '/magento_small_image.jpg', $targetTmpFilePath);
// Copying the image to target dir is not necessary because during product save, it will be moved there from tmp dir

$targetTmpFilePath = $tmpDirectory->getAbsolutePath('magento_empty.jpg');
copy(__DIR__ . '/magento_empty.jpg', $targetTmpFilePath);

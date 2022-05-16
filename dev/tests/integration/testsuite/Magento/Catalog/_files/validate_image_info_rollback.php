<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Filesystem $filesystem */
$filesystem = $objectManager->create(Filesystem::class);

/** @var Config $config */
$config = $objectManager->get(Config::class);

/** @var $tmpDirectory WriteInterface */
$tmpDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);

$targetTmpFilePath = $tmpDirectory->getAbsolutePath($config->getBaseTmpMediaPath() . '/magento_small_image.jpg');
if (file_exists($targetTmpFilePath)) {
    unlink($targetTmpFilePath);
}

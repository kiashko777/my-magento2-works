<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Write $mediaDirectory */

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\TestFramework\Helper\Bootstrap;

$mediaDirectory = Bootstrap::getObjectManager()->get(
    Filesystem::class
)->getDirectoryWrite(
    DirectoryList::MEDIA
);
$mediaDirectory->create('import/m/a');
$dirPath = $mediaDirectory->getAbsolutePath('import');
copy(__DIR__ . '/../../../../../Magento/Catalog/_files/magento_image.jpg', "{$dirPath}/m/a/magento_image.jpg");

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
$mediaDirectory->delete('import');
$mediaDirectory->delete('catalog');

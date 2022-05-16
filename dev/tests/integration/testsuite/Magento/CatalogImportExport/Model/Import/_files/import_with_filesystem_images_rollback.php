<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Filesystem $fileSystem */

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\TestFramework\Helper\Bootstrap;

$fileSystem = Bootstrap::getObjectManager()->get(
    Filesystem::class
);
/** @var Write $mediaDirectory */
$mediaDirectory = $fileSystem->getDirectoryWrite(
    DirectoryList::MEDIA
);
/** @var Write $varDirectory */
$varDirectory = $fileSystem->getDirectoryWrite(
    DirectoryList::VAR_DIR
);
$varDirectory->delete('import');
$mediaDirectory->delete('catalog');

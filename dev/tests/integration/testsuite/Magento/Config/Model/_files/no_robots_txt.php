<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Write $rootDirectory */
$rootDirectory = Bootstrap::getObjectManager()->get(
    Filesystem::class
)->getDirectoryWrite(
    DirectoryList::PUB
);
if ($rootDirectory->isExist('robots.txt')) {
    $rootDirectory->delete('robots.txt');
}

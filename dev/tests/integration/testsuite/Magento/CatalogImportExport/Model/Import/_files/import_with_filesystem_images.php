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

$path = 'catalog' . DIRECTORY_SEPARATOR . 'product';
$varImagesPath = 'import' . DIRECTORY_SEPARATOR . 'images';
// Is required for using importDataForMediaTest method.
$varDirectory->create($varImagesPath);
$mediaDirectory->create($path);
$dirPath = $mediaDirectory->getAbsolutePath($path);

$items = [
    [
        'source' => __DIR__ . '/../../../../../Magento/Catalog/_files/magento_image.jpg',
        'dest' => $dirPath . '/magento_image.jpg',
    ],
    [
        'source' => __DIR__ . '/../../../../../Magento/Catalog/_files/magento_small_image.jpg',
        'dest' => $dirPath . '/magento_small_image.jpg',
    ],
    [
        'source' => __DIR__ . '/../../../../../Magento/Catalog/_files/magento_thumbnail.jpg',
        'dest' => $dirPath . '/magento_thumbnail.jpg',
    ],
    [
        'source' => __DIR__ . '/magento_additional_image_one.jpg',
        'dest' => $dirPath . '/magento_additional_image_one.jpg',
    ],
    [
        'source' => __DIR__ . '/magento_additional_image_two.jpg',
        'dest' => $dirPath . '/magento_additional_image_two.jpg',
    ],
];

foreach ($items as $item) {
    copy($item['source'], $item['dest']);
}

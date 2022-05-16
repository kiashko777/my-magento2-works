<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Analytics\Model\FileInfoManager;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/**
 * @var $fileInfoManager FileInfoManager
 */
$fileInfoManager = $objectManager->create(FileInfoManager::class);

/**
 * @var $fileInfo \Magento\Analytics\Model\FileInfo
 */
$fileInfo = $objectManager->create(
    \Magento\Analytics\Model\FileInfo::class,
    ['path' => 'analytics/jsldjsfdkldf/data.tgz', 'initializationVector' => 'binaryDataisdodssds8iui']
);

$fileInfoManager->save($fileInfo);

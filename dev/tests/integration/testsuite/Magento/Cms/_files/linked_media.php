<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\Filesystem;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$directoryName = 'linked_media';
/** @var Filesystem $filesystem */
$filesystem = $objectManager->get(Filesystem::class);
$fullDirectoryPath = $filesystem->getDirectoryRead(Magento\Framework\App\Filesystem\DirectoryList::PUB)
        ->getAbsolutePath() . $directoryName;
$mediaDirectory = $filesystem->getDirectoryWrite(Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

$wysiwygDir = $mediaDirectory->getAbsolutePath() . 'wysiwyg';
$mediaDirectory->delete($wysiwygDir);
if (!is_dir($fullDirectoryPath)) {
    mkdir($fullDirectoryPath);
}
if (is_dir($fullDirectoryPath) && !is_dir($wysiwygDir)) {
    symlink($fullDirectoryPath, $wysiwygDir);
}

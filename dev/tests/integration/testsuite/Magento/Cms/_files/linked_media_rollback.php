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
$pubDir = $filesystem->getDirectoryWrite(Magento\Framework\App\Filesystem\DirectoryList::PUB);
$fullDirectoryPath = $pubDir->getAbsolutePath() . DIRECTORY_SEPARATOR . $directoryName;
$mediaDirectory = $filesystem->getDirectoryWrite(Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
$wysiwygDir = $mediaDirectory->getAbsolutePath() . 'wysiwyg';
if (is_link($wysiwygDir)) {
    unlink($wysiwygDir);
}
if (is_dir($fullDirectoryPath)) {
    $pubDir->delete($directoryName);
}

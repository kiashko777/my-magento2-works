<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Framework\Config\FileIterator;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\TestFramework\Helper\Bootstrap;

class FileResolverStub implements FileResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        $objectManager = Bootstrap::getObjectManager();
        $fileReadFactory = $objectManager->create(ReadFactory::class);
        $paths = [realpath(__DIR__ . '/../_files/etc/') . '/extension_attributes.xml'];
        return new FileIterator($fileReadFactory, $paths);
    }
}

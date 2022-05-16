<?php
/**
 * Application config file resolver
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Search\Request\Config;

use Magento\Framework\Config\FileIterator;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\ReadFactory;

class FileResolverStub implements FileResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        $path = realpath(__DIR__ . '/../../_files/etc');
        $paths = [$path . '/search_request_1.xml', $path . '/search_request_2.xml'];
        return new FileIterator(
            new ReadFactory(new DriverPool),
            $paths
        );
    }
}

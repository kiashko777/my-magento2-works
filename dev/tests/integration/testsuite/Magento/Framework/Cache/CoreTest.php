<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * \Magento\Framework\Cache\Core test case
 */

namespace Magento\Framework\Cache;

use Magento\Framework\Cache\Backend\Decorator\AbstractDecorator;
use Magento\Framework\Cache\Backend\Decorator\Compression;
use PHPUnit\Framework\TestCase;
use Zend_Cache_Backend_File;
use Zend_Cache_Exception;

class CoreTest extends TestCase
{
    public function testSetBackendSuccess()
    {
        $mockBackend = $this->createMock(Zend_Cache_Backend_File::class);
        $config = [
            'backend_decorators' => [
                'test_decorator' => [
                    'class' => Compression::class,
                    'options' => ['compression_threshold' => '100'],
                ],
            ],
        ];

        $core = new Core($config);
        $core->setBackend($mockBackend);

        $this->assertInstanceOf(
            AbstractDecorator::class,
            $core->getBackend()
        );
    }

    /**
     */
    public function testSetBackendException()
    {
        $this->expectException(Zend_Cache_Exception::class);

        $mockBackend = $this->createMock(Zend_Cache_Backend_File::class);
        $config = ['backend_decorators' => ['test_decorator' => ['class' => 'Zend_Cache_Backend']]];

        $core = new Core($config);
        $core->setBackend($mockBackend);
    }
}

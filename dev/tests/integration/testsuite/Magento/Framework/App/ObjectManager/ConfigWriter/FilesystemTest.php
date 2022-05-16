<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Framework\App\ObjectManager\ConfigWriter;

use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\ObjectManager\ConfigLoader\Compiled;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    const CACHE_KEY = 'filesystemtest';

    /**
     * @var Filesystem
     */
    private $configWriter;

    /**
     * @var ConfigLoader
     */
    private $configReader;

    public function testWrite()
    {
        $sampleData = [
            'classA' => true,
            'classB' => false,
        ];

        $this->configWriter->write(self::CACHE_KEY, $sampleData);
        $this->assertEquals($sampleData, $this->configReader->load(self::CACHE_KEY));
    }

    public function testOverwrite()
    {
        $this->configWriter->write(self::CACHE_KEY, ['hello' => 'world']);

        $sampleData = [
            'classC' => false,
            'classD' => true,
        ];

        $this->configWriter->write(self::CACHE_KEY, $sampleData);
        $this->assertEquals($sampleData, $this->configReader->load(self::CACHE_KEY));
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->configWriter = $objectManager->create(
            Filesystem::class
        );
        $this->configReader = $objectManager->create(
            Compiled::class
        );
    }
}

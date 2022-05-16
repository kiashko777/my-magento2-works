<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\ObjectManager;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigLoaderTest extends TestCase
{
    /**
     * @var ConfigLoader
     */
    private $object;

    public function testLoad()
    {
        $data = $this->object->load('global');
        $this->assertNotEmpty($data);
        $cachedData = $this->object->load('global');
        $this->assertEquals($data, $cachedData);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->object = $objectManager->create(
            ConfigLoader::class
        );
    }
}

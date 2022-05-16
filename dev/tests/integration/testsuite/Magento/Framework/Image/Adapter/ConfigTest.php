<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Image\Adapter;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGetAdapterName()
    {
        /** @var Config $config */
        $config = Bootstrap::getObjectManager()
            ->create(Config::class);
        $this->assertEquals(AdapterInterface::ADAPTER_GD2, $config->getAdapterAlias());
    }
}

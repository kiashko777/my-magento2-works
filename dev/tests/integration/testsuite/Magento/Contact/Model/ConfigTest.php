<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Contact\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var ConfigInterface
     */
    private $configModel;

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store contact/contact/enabled 1
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->configModel->isEnabled());
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store contact/contact/enabled 0
     */
    public function testIsNotEnabled()
    {
        $this->assertFalse($this->configModel->isEnabled());
    }

    protected function setUp(): void
    {
        $this->configModel = Bootstrap::getObjectManager()->create(ConfigInterface::class);
    }
}

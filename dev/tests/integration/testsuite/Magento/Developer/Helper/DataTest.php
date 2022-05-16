<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Developer\Helper;

use Laminas\Stdlib\Parameters;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $helper = null;

    /**
     * @magentoAppIsolation enabled
     */
    public function testIsDevAllowedDefault()
    {
        $this->assertTrue($this->helper->isDevAllowed());
    }

    /**
     * @magentoConfigFixture current_store dev/restrict/allow_ips 192.168.0.1
     * @magentoAppIsolation enabled
     */
    public function testIsDevAllowedTrue()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Request $request */
        $request = $objectManager->get(Request::class);
        $request->setServer(new Parameters(['REMOTE_ADDR' => '192.168.0.1']));

        $this->assertTrue($this->helper->isDevAllowed());
    }

    /**
     * @magentoConfigFixture current_store dev/restrict/allow_ips 192.168.0.1
     * @magentoAppIsolation enabled
     */
    public function testIsDevAllowedFalse()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Request $request */
        $request = $objectManager->get(Request::class);
        $request->setServer(new Parameters(['REMOTE_ADDR' => '192.168.0.3']));

        $this->assertFalse($this->helper->isDevAllowed());
    }

    protected function setUp(): void
    {
        $this->helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}

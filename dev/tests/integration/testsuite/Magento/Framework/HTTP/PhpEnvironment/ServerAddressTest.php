<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\HTTP\PhpEnvironment;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ServerAddressTest extends TestCase
{
    /**
     * @var ServerAddress
     */
    protected $_helper;

    public function testGetServerAddress()
    {
        $this->assertFalse($this->_helper->getServerAddress());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->_helper = $objectManager->get(ServerAddress::class);
    }
}

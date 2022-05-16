<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\HTTP\PhpEnvironment;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class RemoteAddressTest extends TestCase
{
    /**
     * @var RemoteAddress
     */
    protected $_helper;

    public function testGetRemoteAddress()
    {
        $this->assertFalse($this->_helper->getRemoteAddress());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->_helper = $objectManager->get(RemoteAddress::class);
    }
}

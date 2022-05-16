<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Url\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $_helper = null;

    public function testGetCurrentBase64Url()
    {
        $this->assertEquals('aHR0cDovL2xvY2FsaG9zdDo4MS8,', $this->_helper->getCurrentBase64Url());
    }

    public function testGetEncodedUrl()
    {
        $this->assertEquals('aHR0cDovL2xvY2FsaG9zdDo4MS8,', $this->_helper->getEncodedUrl());
        $this->assertEquals('aHR0cDovL2V4YW1wbGUuY29tLw,,', $this->_helper->getEncodedUrl('http://example.com/'));
    }

    protected function setUp(): void
    {
        $this->_helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}

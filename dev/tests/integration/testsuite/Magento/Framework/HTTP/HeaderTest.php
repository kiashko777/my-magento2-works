<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\HTTP;

use Laminas\Stdlib\Parameters;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{
    /**
     * @var Header
     */
    protected $_header;

    public function testGetHttpHeaderMethods()
    {
        $host = 'localhost';
        $this->assertEquals($host, $this->_header->getHttpHost());
        $this->assertEquals('', $this->_header->getHttpUserAgent());
        $this->assertEquals('', $this->_header->getHttpAcceptLanguage());
        $this->assertEquals('', $this->_header->getHttpAcceptCharset());
        $this->assertEquals('', $this->_header->getHttpReferer());
    }

    public function testGetRequestUri()
    {
        $this->assertEquals('/', $this->_header->getRequestUri());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->_header = $objectManager->get(Header::class);

        /** @var Request $request */
        $request = $objectManager->get(Request::class);
        $request->setServer(new Parameters(['HTTP_HOST' => 'localhost']));
    }
}

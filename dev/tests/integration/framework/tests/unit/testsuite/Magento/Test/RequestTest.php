<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test;

use Laminas\Stdlib\Parameters;
use Magento\Framework\App\Request\PathInfoProcessorInterface;
use Magento\Framework\App\Route\ConfigInterface\Proxy;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieReaderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @var Request
     */
    protected $_model = null;

    public function testGetHttpHost()
    {
        $this->assertEquals('localhost', $this->_model->getHttpHost());
        $this->assertEquals('localhost:81', $this->_model->getHttpHost(false));
    }

    public function testSetGetServerValue()
    {
        $this->_model->setServer(new Parameters([]));
        $this->assertSame([], $this->_model->getServer()->toArray());
        $this->assertSame(
            $this->_model,
            $this->_model->setServer(new Parameters(['test' => 'value', 'null' => null]))
        );
        $this->assertSame(['test' => 'value', 'null' => null], $this->_model->getServer()->toArray());
        $this->assertEquals('value', $this->_model->getServer('test'));
        $this->assertNull($this->_model->getServer('non-existing'));
        $this->assertSame('default', $this->_model->getServer('non-existing', 'default'));
        $this->assertNull($this->_model->getServer('null'));
    }

    protected function setUp(): void
    {
        $this->_model = new Request(
            $this->createMock(CookieReaderInterface::class),
            $this->createMock(StringUtils::class),
            $this->createMock(Proxy::class),
            $this->createMock(PathInfoProcessorInterface::class),
            $this->createMock(ObjectManagerInterface::class)
        );
    }
}

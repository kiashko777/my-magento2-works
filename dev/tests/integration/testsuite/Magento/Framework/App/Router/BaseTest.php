<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Router;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Index;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    /**
     * @var Base
     */
    protected $_model;

    /**
     * @magentoAppArea frontend
     */
    public function testMatch()
    {
        if (!Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get match without sending headers');
        }

        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);

        $this->assertInstanceOf(ActionInterface::class, $this->_model->match($request));
        $request->setRequestUri('framework/index/index');
        $this->assertInstanceOf(ActionInterface::class, $this->_model->match($request));

        $request->setPathInfo(
            'not_exists/not_exists/not_exists'
        )->setModuleName(
            'not_exists'
        )->setControllerName(
            'not_exists'
        )->setActionName(
            'not_exists'
        );
        $this->assertNull($this->_model->match($request));
    }

    public function testGetControllerClassName()
    {
        $this->assertEquals(
            Index::class,
            $this->_model->getActionClassName('Magento_Framework', 'index')
        );
    }

    protected function setUp(): void
    {
        $options = ['routerId' => 'standard'];
        $this->_model = Bootstrap::getObjectManager()->create(
            Base::class,
            $options
        );
    }
}

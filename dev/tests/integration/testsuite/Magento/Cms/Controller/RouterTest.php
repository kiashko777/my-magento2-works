<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Controller;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Config;
use Magento\Framework\Event\InvokerInterface;
use Magento\Framework\Event\ManagerInterfaceStub;
use Magento\Framework\Event\ObserverFactory;
use Magento\Framework\EventFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    protected $_model;

    /**
     * @magentoAppIsolation enabled
     */
    public function testMatch()
    {
        $this->markTestIncomplete('MAGETWO-3393');
        $request = Bootstrap::getObjectManager()
            ->create(RequestInterface::class);
        //Open Node
        $request->setPathInfo('parent_node');
        $controller = $this->_model->match($request);
        $this->assertInstanceOf(Redirect::class, $controller);
    }

    protected function setUp(): void
    {
        $this->markTestIncomplete('MAGETWO-3393');
        $this->_model = new Router(
            Bootstrap::getObjectManager()->get(
                ActionFactory::class
            ),
            new ManagerInterfaceStub(
                $this->getMockForAbstractClass(InvokerInterface::class),
                $this->createMock(Config::class),
                $this->createMock(EventFactory::class),
                $this->createMock(ObserverFactory::class)
            ),
            Bootstrap::getObjectManager()->get(UrlInterface::class),
            Bootstrap::getObjectManager()->get(PageFactory::class),
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            ),
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )
        );
    }
}
/**
 * Event manager stub
 * @codingStandardsIgnoreStart
 */

namespace Magento\Framework\Event;

class ManagerStub extends Manager
{
    /**
     * Stub dispatch event
     *
     * @param string $eventName
     * @param array $params
     * @return null
     */
    public function dispatch($eventName, array $params = [])
    {
        switch ($eventName) {
            case 'cms_controller_router_match_before':
                $params['condition']->setRedirectUrl('http://www.example.com/');
                break;
        }

        return null;
    }
}

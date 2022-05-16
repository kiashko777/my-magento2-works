<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\App;

use Magento\Backend\Controller\Adminhtml\Dashboard;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Route\Config\Reader;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFixture\Controller\Adminhtml\Noroute;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use Magento\TestModule\Controller\Adminhtml\Controller;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    protected $model;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function testRouterCanProcessRequestsWithProperPathInfo()
    {
        $request = $this->createMock(Http::class);
        $request->expects($this->once())->method('getPathInfo')->willReturn('backend/admin/dashboard');

        $this->assertInstanceOf(Dashboard::class, $this->model->match($request));
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $className
     *
     * @dataProvider getControllerClassNameDataProvider
     */
    public function testGetControllerClassName($module, $controller, $className)
    {
        $this->assertEquals($className, $this->model->getActionClassName($module, $controller));
    }

    public function getControllerClassNameDataProvider()
    {
        return [
            ['Magento_TestModule', 'controller', Controller::class],
        ];
    }

    public function testMatchCustomNoRouteAction()
    {
        if (!Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get match without sending headers');
        }

        $routers = [
            'testmodule' => [
                'frontName' => 'testfixture',
                'id' => 'testfixture',
                'modules' => ['Magento_TestFixture'],
            ],
        ];

        $routeConfig = $this->getMockBuilder(\Magento\Framework\App\Route\Config::class)
            ->setMethods(['_getRoutes'])
            ->setConstructorArgs(
                [
                    'reader' => $this->objectManager->get(Reader::class),
                    'cache' => $this->objectManager->get(CacheInterface::class),
                    'configScope' => $this->objectManager->get(ScopeInterface::class),
                    'areaList' => $this->objectManager->get(AreaList::class),
                    'cacheId' => 'RoutesConfig'
                ]
            )
            ->getMock();

        $routeConfig->expects($this->any())->method('_getRoutes')->willReturn($routers);

        $defaultRouter = $this->objectManager->create(
            Router::class,
            ['routeConfig' => $routeConfig]
        );

        /** @var $request Request */
        $request = $this->objectManager->get(Request::class);

        $request->setPathInfo('backend/testfixture/test_controller');
        $controller = $defaultRouter->match($request);
        $this->assertInstanceOf(Noroute::class, $controller);
        $this->assertEquals('noroute', $request->getActionName());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(Router::class);
    }
}

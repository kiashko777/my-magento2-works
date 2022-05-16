<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Helper\Product;

use Magento\Catalog\Helper\Product\Stub\ProductControllerStub;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Session;
use Magento\Customer\Model\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use Magento\TestFramework\Response;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ViewTest extends TestCase
{
    /**
     * @var View
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Controller\Product
     */
    protected $_controller;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testInitProductLayout()
    {
        $uniqid = uniqid();
        /** @var $product Product */
        $product = $this->objectManager->create(Product::class);
        $product->setTypeId(Type::DEFAULT_TYPE)->setId(99)->setUrlKey($uniqid);
        /** @var $objectManager ObjectManager */
        $objectManager = $this->objectManager;
        $objectManager->get(Registry::class)->register('product', $product);

        $this->_helper->initProductLayout($this->page, $product);

        /** @var Config $pageConfig */
        $pageConfig = $this->objectManager->get(Config::class);
        $bodyClass = $pageConfig->getElementAttribute(
            Config::ELEMENT_TYPE_BODY,
            Config::BODY_ATTRIBUTE_CLASS
        );
        $this->assertStringContainsString("product-{$uniqid}", $bodyClass);
        $handles = $this->page->getLayout()->getUpdate()->getHandles();
        $this->assertContains('catalog_product_view_type_simple', $handles);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testPrepareAndRender()
    {
        // need for \Magento\Review\Block\Form::getProductInfo()
        $this->objectManager->get(RequestInterface::class)->setParam('id', 10);

        $this->_helper->prepareAndRender($this->page, 10, $this->_controller);
        /** @var Response $response */
        $response = $this->objectManager->get(Response::class);
        $this->page->renderResult($response);
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals(
            10,
            $this->objectManager->get(
                Session::class
            )->getLastViewedProductId()
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareAndRenderWrongController()
    {
        $this->expectException(NoSuchEntityException::class);

        $objectManager = $this->objectManager;
        $controller = $objectManager->create(ProductControllerStub::class);
        $this->_helper->prepareAndRender($this->page, 10, $controller);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareAndRenderWrongProduct()
    {
        $this->expectException(NoSuchEntityException::class);

        $this->_helper->prepareAndRender($this->page, 999, $this->_controller);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->objectManager->get(State::class)->setAreaCode('frontend');
        $this->objectManager->get(\Magento\Framework\App\Http\Context::class)
            ->setValue(Context::CONTEXT_AUTH, false, false);
        $this->objectManager->get(DesignInterface::class)
            ->setDefaultDesignTheme();
        $this->_helper = $this->objectManager->get(View::class);
        $request = $this->objectManager->get(Request::class);
        $request->setRouteName('catalog')->setControllerName('product')->setActionName('view');
        $arguments = [
            'request' => $request,
            'response' => $this->objectManager->get(Response::class),
        ];
        $context = $this->objectManager->create(\Magento\Framework\App\Action\Context::class, $arguments);
        $this->_controller = $this->objectManager->create(
            ProductControllerStub::class,
            ['context' => $context]
        );
        $resultPageFactory = $this->objectManager->get(PageFactory::class);
        $this->page = $resultPageFactory->create();
    }

    /**
     * Cleanup session, contaminated by product initialization methods
     */
    protected function tearDown(): void
    {
        $this->objectManager->get(Session::class)->unsLastViewedProductId();
        $this->_controller = null;
        $this->_helper = null;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Magento\Customer\Block\Adminhtml\Edit\Tab\Carts
 *
 * @magentoAppArea Adminhtml
 */
class CartsTest extends TestCase
{
    /** @var Carts */
    private $_block;

    /** @var CustomerRepositoryInterface */
    private $_customerRepository;

    /** @var Context */
    private $_context;

    /** @var ObjectManagerInterface */
    private $_objectManager;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetHtml()
    {
        $customer = $this->_customerRepository->getById(1);
        $data = ['account' => $customer->__toArray()];
        $this->_context->getBackendSession()->setCustomerData($data);

        $this->_block = $this->_objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Carts::class,
            '',
            ['context' => $this->_context]
        );

        $html = $this->_block->toHtml();
        $this->assertStringContainsString("<div id=\"customer_cart_grid\"", $html);
        $this->assertMatchesRegularExpression(
            '/<div class=".*admin__data-grid-toolbar"/',
            $html
        );
        $this->assertStringContainsString("customer_cart_gridJsObject = new varienGrid(\"customer_cart_grid\",", $html);
        $this->assertStringContainsString(
            'backend\u002Fcustomer\u002Fcart_product_composite_cart\u002Fconfigure\u002Fwebsite_id\u002F1',
            $html
        );
    }

    public function testGetHtmlNoCustomer()
    {
        $data = ['account' => []];
        $this->_context->getBackendSession()->setCustomerData($data);

        $this->_block = $this->_objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Carts::class,
            '',
            ['context' => $this->_context]
        );

        $html = $this->_block->toHtml();
        $this->assertStringContainsString("<div id=\"customer_cart_grid\"", $html);
        $this->assertMatchesRegularExpression(
            '/<div class=".*admin__data-grid-toolbar"/',
            $html
        );
        $this->assertStringContainsString("customer_cart_gridJsObject = new varienGrid(\"customer_cart_grid\",", $html);
        $this->assertStringContainsString(
            'backend\u002Fcustomer\u002Fcart_product_composite_cart\u002Fupdate\u002Fkey',
            $html
        );
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_customerRepository = $this->_objectManager->get(
            CustomerRepositoryInterface::class
        );
        $storeManager = $this->_objectManager->get(StoreManager::class);
        $this->_context = $this->_objectManager->create(
            Context::class,
            ['storeManager' => $storeManager]
        );
    }
}

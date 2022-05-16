<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Customer\Block\Adminhtml\Edit\Tab\View\Cart
 *
 * @magentoAppArea Adminhtml
 */
class CartTest extends TestCase
{
    /**
     * Shopping cart.
     *
     * @var Cart
     */
    private $block;

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Verify that the Url for a product row in the cart grid is correct.
     */
    public function testGetRowUrl()
    {
        $row = new DataObject(['product_id' => 1]);
        $this->assertStringContainsString('catalog/product/edit/id/1', $this->block->getRowUrl($row));
    }

    /**
     * Verify that the headers in the cart grid are visible.
     */
    public function testGetHeadersVisibility()
    {
        $this->assertTrue($this->block->getHeadersVisibility());
    }

    /**
     * Verify the basic content of an empty cart.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtmlEmptyCart()
    {
        $this->assertEquals(0, $this->block->getCollection()->getSize());
        $this->assertStringContainsString(
            $this->escaper->escapeHtml('There are no items in customer\'s shopping cart.'),
            $this->block->toHtml()
        );
    }

    /**
     * Verify the Html content for a single item in the customer's cart.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/quote.php
     */
    public function testToHtmlCartItem()
    {
        $html = $this->block->toHtml();
        $this->assertStringContainsString('Simple Products', $html);
        $this->assertStringContainsString('simple', $html);
        $this->assertStringContainsString('$10.00', $html);
        $this->assertStringContainsString($this->escaper->escapeHtmlAttr('catalog/product/edit/id/1'), $html);
    }

    /**
     * Execute per test initialization.
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode('Adminhtml');

        $this->coreRegistry = $objectManager->get(Registry::class);
        $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);

        $this->block = $objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Cart::class,
            '',
            ['coreRegistry' => $this->coreRegistry, 'data' => ['website_id' => 1]]
        );
        $this->block->getPreparedCollection();
        $this->escaper = $objectManager->get(Escaper::class);
    }

    /**
     * Execute per test cleanup.
     */
    protected function tearDown(): void
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
}

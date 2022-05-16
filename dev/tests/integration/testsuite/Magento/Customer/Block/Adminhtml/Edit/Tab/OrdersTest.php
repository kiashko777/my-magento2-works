<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Customer\Block\Adminhtml\Edit\Tab\Orders
 *
 * @magentoAppArea Adminhtml
 */
class OrdersTest extends TestCase
{
    /**
     * The orders block under test.
     *
     * @var Orders
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
     * Verify that a valid Url is returned for a given sales order row.
     */
    public function testGetRowUrl()
    {
        $row = new DataObject(['id' => 1]);
        $this->assertStringContainsString('sales/order/view/order_id/1', $this->block->getRowUrl($row));
    }

    /**
     * Verify that a valid grid Url is returned.
     */
    public function testGetGridUrl()
    {
        $this->assertStringContainsString('customer/index/orders', $this->block->getGridUrl());
    }

    /**
     * Verify that the sales order grid Html is valid and contains no records.
     */
    public function testToHtml()
    {
        $this->assertStringContainsString(
            $this->escaper->escapeHtml("We couldn't find any records."),
            $this->block->toHtml()
        );
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
            Orders::class,
            '',
            ['coreRegistry' => $this->coreRegistry]
        );
        $this->block->getPreparedCollection();
        $this->escaper = $objectManager->get(Escaper::class);
    }

    /**
     * Execute post test cleanup.
     */
    protected function tearDown(): void
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
        $this->block->setCollection(null);
    }
}

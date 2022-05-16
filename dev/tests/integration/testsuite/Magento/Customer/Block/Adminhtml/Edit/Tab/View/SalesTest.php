<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Customer\Block\Adminhtml\Edit\Tab\View\Sales
 *
 * @magentoAppArea Adminhtml
 */
class SalesTest extends TestCase
{
    const MAIN_WEBSITE = 1;

    /**
     * Sales block under test.
     *
     * @var Sales
     */
    private $block;

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Sales order view Html.
     *
     * @var string
     */
    private $html;

    /**
     * Test basic currency formatting on the Main Website.
     */
    public function testFormatCurrency()
    {
        $this->assertEquals(
            '<span class="price">$10.00</span>',
            $this->block->formatCurrency(10.00, self::MAIN_WEBSITE)
        );
    }

    /**
     * Verify that the website is not in single store mode.
     */
    public function testIsSingleStoreMode()
    {
        $this->assertFalse($this->block->isSingleStoreMode());
    }

    /**
     * Verify sales totals. No sales so there are no totals.
     */
    public function testGetTotals()
    {
        $this->assertEquals(
            ['lifetime' => 0, 'base_lifetime' => 0, 'base_avgsale' => 0, 'num_orders' => 0],
            $this->block->getTotals()->getData()
        );
    }

    /**
     * Verify that there are no rows in the sales order grid.
     */
    public function testGetRows()
    {
        $this->assertEmpty($this->block->getRows());
    }

    /**
     * Verify that the Main Website has no websites.
     */
    public function testGetWebsiteCount()
    {
        $this->assertEquals(0, $this->block->getWebsiteCount(self::MAIN_WEBSITE));
    }

    /**
     * Verify basic content of the sales view Html.
     */
    public function testToHtml()
    {
        $this->assertStringContainsString('<span class="title">Sales Statistics</span>', $this->html);
        $this->assertStringContainsString('<strong>All Store Views</strong>', $this->html);
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
            Sales::class,
            'sales_' . random_int(0, PHP_INT_MAX),
            ['coreRegistry' => $this->coreRegistry]
        )->setTemplate(
            'tab/view/sales.phtml'
        );
        $this->html = $this->block->toHtml();
    }

    /**
     * Execute post test cleanup.
     */
    protected function tearDown(): void
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
        $this->html = '';
    }
}

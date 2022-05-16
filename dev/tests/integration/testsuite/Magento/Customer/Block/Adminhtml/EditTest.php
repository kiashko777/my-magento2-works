<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Customer\Block\Adminhtml\Edit
 *
 * @magentoAppArea Adminhtml
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class EditTest extends TestCase
{
    /**
     * The customer Id.
     *
     * @var int
     */
    private static $customerId = 1;
    /**
     * The edit block under test.
     *
     * @var Edit
     */
    private $block;
    /**
     * Core Registry.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Verify that the customer Id is the one that was set in the registry.
     */
    public function testGetCustomerId()
    {
        $this->assertEquals(self::$customerId, $this->block->getCustomerId());
    }

    /**
     * Verify that the correct order create Url is generated.
     */
    public function testGetCreateOrderUrl()
    {
        $this->assertStringContainsString(
            'sales/order_create/start/customer_id/' . self::$customerId,
            $this->block->getCreateOrderUrl()
        );
    }

    /**
     * Verify that the header text is correct for a new customer.
     */
    public function testGetHeaderTextNewCustomer()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
        $this->assertEquals('New Customer', $this->block->getHeaderText());
    }

    /**
     * Verify that the header text is correct for an existing customer.
     */
    public function testGetHeaderTextExistingCustomer()
    {
        $this->assertEquals('John Smith', $this->block->getHeaderText());
    }

    /**
     * Verify that the correct customer validation Url is generated.
     */
    public function testGetValidationUrl()
    {
        $this->assertStringContainsString('customer/index/validate', $this->block->getValidationUrl());
    }

    /**
     * Verify the basic content of the block's form Html.
     */
    public function testGetFormHtml()
    {
        $html = $this->block->getFormHtml();
        $this->assertStringContainsString('<div class="entry-edit form-inline">', $html);
        $this->assertStringMatchesFormat('%a name="customer_id" %s value="' . self::$customerId . '" %a', $html);
        $this->assertStringContainsString('id="product_composite_configure_form"', $html);
    }

    /**
     * Execute per test initialization.
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode('Adminhtml');

        $this->coreRegistry = $objectManager->get(Registry::class);
        $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, self::$customerId);

        $this->block = $objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Edit::class,
            '',
            ['coreRegistry' => $this->coreRegistry]
        );
    }

    /**
     * Execute post class cleanup after all tests have executed.
     */
    protected function tearDown(): void
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
}

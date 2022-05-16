<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * Tax helper
     *
     * @var Data
     */
    private $helper;

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoConfigFixture default_store tax/classes/default_customer_tax_class 1
     */
    public function testGetDefaultCustomerTaxClass()
    {
        $this->assertEquals(1, $this->helper->getDefaultCustomerTaxClass());
    }

    /**
     * @magentoConfigFixture default_store tax/classes/default_product_tax_class 1
     */
    public function testGetDefaultProductTaxClass()
    {
        $this->assertEquals(1, $this->helper->getDefaultProductTaxClass());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        /** @var $helper Data */
        $this->helper = $this->objectManager->get(Data::class);
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Block\Adminhtml\Report\Filter\Form;

use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class CouponTest extends TestCase
{
    /**
     * Layout
     *
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @covers \Magento\Sales\Block\Adminhtml\Report\Filter\Form\Coupon::_afterToHtml
     */
    public function testAfterToHtml()
    {
        /** @var $block Coupon */
        $block = $this->_layout->createBlock(Coupon::class);
        $block->setFilterData(new DataObject());
        $html = $block->toHtml();

        $expectedStrings = [
            'FormElementDependenceController',
            'sales_report_rules_list',
            'sales_report_price_rule_type',
        ];
        foreach ($expectedStrings as $expectedString) {
            $this->assertStringContainsString($expectedString, $html);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_layout = Bootstrap::getObjectManager()
            ->get(LayoutInterface::class);
    }
}

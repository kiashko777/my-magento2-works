<?php
/**
 * Test for \Magento\Paypal\Block\Payment\Form\Billing\Agreement
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Block\Payment\Form\Billing;

use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AgreementTest extends TestCase
{
    /** @var Agreement */
    protected $_block;

    /**
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testGetBillingAgreements()
    {
        $billingAgreements = $this->_block->getBillingAgreements();
        $this->assertCount(1, $billingAgreements);
        $this->assertEquals('REF-ID-TEST-678', array_shift($billingAgreements));
    }

    protected function setUp(): void
    {
        $quote = Bootstrap::getObjectManager()->create(
            Collection::class
        )->getFirstItem();
        /** @var LayoutInterface $layout */
        $layout = $this->getMockBuilder(LayoutInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layout->expects(
            $this->once()
        )->method(
            'getBlock'
        )->willReturn(
            new DataObject(['quote' => $quote])
        );
        $layout->expects($this->once())->method('getParentName')->willReturn('billing_agreement_form');

        $this->_block = Bootstrap::getObjectManager()->create(
            Agreement::class
        );
        $this->_block->setLayout($layout);
    }
}

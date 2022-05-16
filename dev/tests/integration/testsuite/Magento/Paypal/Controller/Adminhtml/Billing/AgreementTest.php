<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Controller\Adminhtml\Billing;

use Magento\TestFramework\Helper\Xpath;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Test class for \Magento\Paypal\Controller\Adminhtml\Billing\Agreement
 *
 * @magentoAppArea Adminhtml
 */
class AgreementTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testCustomerGrid()
    {
        $this->dispatch('backend/paypal/billing_agreement/customergrid/id/1');
        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//th[contains(@class,"col-reference_id")]',
                $this->getResponse()->getBody()
            ),
            "Response for billing agreement orders doesn't contain billing agreement customers grid"
        );
        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//td[contains(text(), "REF-ID-TEST-678")]',
                $this->getResponse()->getBody()
            ),
            "Response for billing agreement info doesn't contain reference ID"
        );
    }
}

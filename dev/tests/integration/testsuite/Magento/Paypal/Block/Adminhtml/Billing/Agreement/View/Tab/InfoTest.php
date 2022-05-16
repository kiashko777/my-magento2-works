<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Block\Adminhtml\Billing\Agreement\View\Tab;

use Magento\Paypal\Model\ResourceModel\Billing\Agreement\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Xpath;
use Magento\TestFramework\TestCase\AbstractBackendController;

class InfoTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testCustomerGridAction()
    {
        /** @var Collection $billingAgreementCollection */
        $billingAgreementCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        )->load();
        $agreementId = $billingAgreementCollection->getFirstItem()->getId();
        $this->dispatch('backend/paypal/billing_agreement/view/agreement/' . $agreementId);

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//a[@name="billing_agreement_info"]',
                $this->getResponse()->getBody()
            ),
            'Response for billing agreement info doesn\'t contain billing agreement info tab'
        );

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//a[contains(text(), "customer@example.com")]',
                $this->getResponse()->getBody()
            ),
            'Response for billing agreement info doesn\'t contain Customer info'
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Controller\Adminhtml\Billing\Agreement;

use Magento\Paypal\Model\ResourceModel\Billing\Agreement\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Xpath;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class ViewTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testAclHasAccess()
    {
        /** @var Collection $billingAgreementCollection */
        $billingAgreementCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $agreementId = $billingAgreementCollection->getFirstItem()->getId();
        $this->uri = $this->uri . '/agreement/' . $agreementId;

        parent::testAclHasAccess();

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//a[@name="billing_agreement_info"]',
                $this->getResponse()->getBody()
            ),
            "Response for billing agreement info doesn't contain billing agreement info tab"
        );

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//a[contains(text(), "customer@example.com")]',
                $this->getResponse()->getBody()
            ),
            "Response for billing agreement info doesn't contain Customer info"
        );
    }

    protected function setUp(): void
    {
        $this->resource = 'Magento_Paypal::billing_agreement_actions_view';
        $this->uri = 'backend/paypal/billing_agreement/view';
        parent::setUp();
    }
}

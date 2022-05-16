<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Controller\Adminhtml\Billing\Agreement;

use Magento\Backend\Model\Session;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Xpath;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class GridTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testAclHasAccess()
    {
        /** @var $session Session */
        Bootstrap::getObjectManager()->create(Session::class);

        parent::testAclHasAccess();

        $response = $this->getResponse();

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//button[@type="button" and @title="Reset Filter"]',
                $response->getBody()
            ),
            "Response for billing agreement grid doesn't contain 'Reset Filter' button"
        );

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//*[@id="billing_agreements"]',
                $response->getBody()
            ),
            "Response for billing agreement grid doesn't contain grid"
        );
    }

    protected function setUp(): void
    {
        $this->resource = 'Magento_Paypal::billing_agreement_actions_view';
        $this->uri = 'backend/paypal/billing_agreement/grid';
        parent::setUp();
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Controller\Adminhtml\Billing\Agreement;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class IndexTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Magento_Paypal::billing_agreement_actions_view';
        $this->uri = 'backend/paypal/billing_agreement/index';
        parent::setUp();
    }
}

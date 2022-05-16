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
class DeleteTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Magento_Paypal::actions_manage';
        $this->uri = 'backend/paypal/billing_agreement/delete';
        parent::setUp();
    }
}

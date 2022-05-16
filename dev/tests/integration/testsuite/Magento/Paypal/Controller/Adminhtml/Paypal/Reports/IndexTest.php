<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Controller\Adminhtml\Paypal\Reports;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class IndexTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Magento_Paypal::paypal_settlement_reports_view';
        $this->uri = 'backend/paypal/paypal_reports/index';
        parent::setUp();
    }
}

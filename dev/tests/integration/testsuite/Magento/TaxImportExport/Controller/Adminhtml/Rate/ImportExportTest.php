<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TaxImportExport\Controller\Adminhtml\Rate;

use Magento\TestFramework\TestCase\AbstractBackendController;

class ImportExportTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Magento_Sales::transactions_fetch';
        $this->uri = 'backend/sales/transactions/fetch';
        parent::setUp();
    }
}

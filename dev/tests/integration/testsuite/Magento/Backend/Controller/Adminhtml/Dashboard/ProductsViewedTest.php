<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml\Dashboard;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Test product viewed backend controller.
 */
class ProductsViewedTest extends AbstractBackendController
{
    /**
     * @magentoAppArea Adminhtml
     * @magentoDataFixture Magento/Reports/_files/viewed_products.php
     * @magentoConfigFixture default/reports/options/enabled 1
     */
    public function testExecute()
    {
        $this->getRequest()->setMethod("POST");
        $this->dispatch('backend/admin/dashboard/productsViewed/');

        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());

        $actual = $this->getResponse()->getBody();
        $this->assertStringContainsString('Simple Products', $actual);
    }
}

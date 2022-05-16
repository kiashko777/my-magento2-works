<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class InvoiceEmailTest
 */
class InvoiceEmailTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';

    const SERVICE_NAME = 'salesInvoiceManagementV1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceEmail()
    {
        $objectManager = Bootstrap::getObjectManager();
        $invoiceCollection = $objectManager->get(Collection::class);
        $invoice = $invoiceCollection->getFirstItem();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoices/' . $invoice->getId() . '/emails',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'notify',
            ],
        ];
        $requestData = ['id' => $invoice->getId()];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
    }
}

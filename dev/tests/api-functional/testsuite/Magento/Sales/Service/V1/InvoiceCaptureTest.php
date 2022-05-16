<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order\Invoice;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class InvoiceCaptureTest
 */
class InvoiceCaptureTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';

    const SERVICE_NAME = 'salesInvoiceManagementV1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceCapture()
    {
        $this->expectException(Exception::class);

        $objectManager = Bootstrap::getObjectManager();
        /** @var Invoice $invoice */
        $invoice = $objectManager->get(Invoice::class)->loadByIncrementId('100000001');
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoices/' . $invoice->getId() . '/capture',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'setCapture',
            ],
        ];
        $requestData = ['id' => $invoice->getId()];
        $this->_webApiCall($serviceInfo, $requestData);
    }
}

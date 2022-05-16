<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class InvoiceGetTest
 */
class InvoiceGetTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/invoices';

    const SERVICE_READ_NAME = 'salesInvoiceRepositoryV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceGet()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Invoice $invoice */
        $invoiceCollection = $objectManager->get(Collection::class);
        $invoice = $invoiceCollection->getFirstItem();
        $expectedInvoiceData = [
            'grand_total' => '100.0000',
            'subtotal' => '100.0000',
            'increment_id' => $invoice->getIncrementId(),
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $invoice->getId(),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'get',
            ],
        ];
        $result = $this->_webApiCall($serviceInfo, ['id' => $invoice->getId()]);
        foreach ($expectedInvoiceData as $field => $value) {
            $this->assertArrayHasKey($field, $result);
            $this->assertEquals($value, $result[$field]);
        }

        //check that nullable fields were marked as optional and were not sent
        foreach ($result as $value) {
            $this->assertNotNull($value);
        }
    }
}

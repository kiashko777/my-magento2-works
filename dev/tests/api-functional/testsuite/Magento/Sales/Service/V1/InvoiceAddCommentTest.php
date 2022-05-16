<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\InvoiceCommentInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class InvoiceAddCommentTest
 */
class InvoiceAddCommentTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesInvoiceCommentRepositoryV1';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Test invoice add comment service
     *
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceAddComment()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Invoice $invoice */
        $invoiceCollection = $objectManager->get(Collection::class);
        $invoice = $invoiceCollection->getFirstItem();

        $commentData = [
            InvoiceCommentInterface::COMMENT => 'Hello world!',
            InvoiceCommentInterface::ENTITY_ID => null,
            InvoiceCommentInterface::CREATED_AT => null,
            InvoiceCommentInterface::PARENT_ID => $invoice->getId(),
            InvoiceCommentInterface::IS_VISIBLE_ON_FRONT => 1,
            InvoiceCommentInterface::IS_CUSTOMER_NOTIFIED => 1,
        ];

        $requestData = ['entity' => $commentData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoices/comments',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'save',
            ],
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotEmpty($result);
    }
}

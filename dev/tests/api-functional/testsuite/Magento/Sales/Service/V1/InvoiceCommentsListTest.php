<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order\Invoice\Comment;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class InvoiceCommentsListTest
 */
class InvoiceCommentsListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'salesInvoiceManagementV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceCommentsList()
    {
        $comment = 'Test comment';
        $objectManager = Bootstrap::getObjectManager();

        /** @var Collection $invoiceCollection */
        $invoiceCollection = $objectManager->get(Collection::class);
        $invoice = $invoiceCollection->getFirstItem();
        $invoiceComment = $objectManager->get(Comment::class);
        $invoiceComment->setComment($comment);
        $invoiceComment->setParentId($invoice->getId());
        $invoiceComment->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoices/' . $invoice->getId() . '/comments',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getCommentsList',
            ],
        ];
        $requestData = ['id' => $invoice->getId()];
        // TODO Test fails, due to the inability of the framework API to handle data collection
        $result = $this->_webApiCall($serviceInfo, $requestData);
        foreach ($result['items'] as $item) {
            /** @var Comment $invoiceHistoryStatus */
            $invoiceHistoryStatus = $objectManager->get(Comment::class)
                ->load($item['entity_id']);
            $this->assertEquals($invoiceHistoryStatus->getComment(), $item['comment']);
        }
    }
}

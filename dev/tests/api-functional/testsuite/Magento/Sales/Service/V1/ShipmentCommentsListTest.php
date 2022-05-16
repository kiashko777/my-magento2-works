<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order\Shipment\Comment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class ShipmentCommentsListTest
 */
class ShipmentCommentsListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'salesShipmentManagementV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testShipmentCommentsList()
    {
        $comment = 'Test comment';
        $objectManager = Bootstrap::getObjectManager();

        /** @var Collection $shipmentCollection */
        $shipmentCollection = $objectManager->get(Collection::class);
        $shipment = $shipmentCollection->getFirstItem();
        $shipmentComment = $objectManager->get(Comment::class);
        $shipmentComment->setComment($comment);
        $shipmentComment->setParentId($shipment->getId());
        $shipmentComment->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/shipment/' . $shipment->getId() . '/comments',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getCommentsList',
            ],
        ];
        $requestData = ['id' => $shipment->getId()];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        // TODO Test fails, due to the inability of the framework API to handle data collection
        foreach ($result['items'] as $item) {
            /** @var Comment $shipmentHistoryStatus */
            $shipmentHistoryStatus = $objectManager->get(Comment::class)
                ->load($item['entity_id']);
            $this->assertEquals($shipmentHistoryStatus->getComment(), $item['comment']);
        }
    }
}

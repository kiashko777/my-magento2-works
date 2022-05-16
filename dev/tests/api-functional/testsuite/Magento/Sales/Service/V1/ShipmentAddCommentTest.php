<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\ShipmentCommentInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class ShipmentAddCommentTest
 */
class ShipmentAddCommentTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesShipmentCommentRepositoryV1';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Shipment increment id
     */
    const SHIPMENT_INCREMENT_ID = '100000001';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Test shipment add comment service
     *
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testShipmentAddComment()
    {
        /** @var Collection $shipmentCollection */
        $shipmentCollection = $this->objectManager->get(
            Collection::class
        );
        $shipment = $shipmentCollection->getFirstItem();

        $commentData = [
            ShipmentCommentInterface::COMMENT => 'Hello world!',
            ShipmentCommentInterface::ENTITY_ID => null,
            ShipmentCommentInterface::CREATED_AT => null,
            ShipmentCommentInterface::PARENT_ID => $shipment->getId(),
            ShipmentCommentInterface::IS_VISIBLE_ON_FRONT => 1,
            ShipmentCommentInterface::IS_CUSTOMER_NOTIFIED => 1,
        ];

        $requestData = ['entity' => $commentData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/shipment/' . $shipment->getId() . '/comments',
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

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}

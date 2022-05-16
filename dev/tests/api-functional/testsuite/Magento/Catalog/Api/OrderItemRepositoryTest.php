<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Api;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class OrderItemRepositoryTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/orders/items';

    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'salesOrderItemRepositoryV1';

    const ORDER_INCREMENT_ID = '100000001';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/order_item_with_product_and_custom_options.php
     */
    public function testGet()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        $order->loadByIncrementId(self::ORDER_INCREMENT_ID);
        $orderItem = current($order->getItems());

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $orderItem->getId(),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, ['id' => $orderItem->getId()]);

        $this->assertIsArray($response);
        $this->assertOrderItem($orderItem, $response);
    }

    /**
     * @param Item $orderItem
     * @param array $response
     * @return void
     */
    protected function assertOrderItem(Item $orderItem, array $response)
    {
        $expected = $orderItem->getBuyRequest()->getOptions();

        $this->assertArrayHasKey('product_option', $response);
        $this->assertArrayHasKey('extension_attributes', $response['product_option']);
        $this->assertArrayHasKey('custom_options', $response['product_option']['extension_attributes']);

        $actualOptions = $response['product_option']['extension_attributes']['custom_options'];

        $expectedOptions = [];
        foreach ($expected as $optionId => $optionValue) {
            if (is_array($optionValue)) {
                $optionValue = implode(',', $optionValue);
            }
            $expectedOptions[] = [
                'option_id' => $optionId,
                'option_value' => $optionValue,
            ];
        }
        $this->assertEquals($expectedOptions, $actualOptions);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/order_item_with_product_and_custom_options.php
     */
    public function testGetList()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        $order->loadByIncrementId(self::ORDER_INCREMENT_ID);

        /** @var $searchCriteriaBuilder  SearchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        /** @var $filterBuilder  FilterBuilder */
        $filterBuilder = $this->objectManager->create(FilterBuilder::class);

        $searchCriteriaBuilder->addFilters(
            [
                $filterBuilder->setField('order_id')
                    ->setValue($order->getId())
                    ->create(),
            ]
        );

        $requestData = ['searchCriteria' => $searchCriteriaBuilder->create()->__toArray()];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('items', $response);
        $this->assertCount(1, $response['items']);
        $this->assertIsArray($response['items'][0]);
        $this->assertOrderItem(current($order->getItems()), $response['items'][0]);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}

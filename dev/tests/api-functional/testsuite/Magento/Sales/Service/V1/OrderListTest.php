<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class OrderListTest
 * @package Magento\Sales\Service\V1
 */
class OrderListTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/orders';

    const SERVICE_READ_NAME = 'salesOrderRepositoryV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order_list.php
     */
    public function testOrderList()
    {
        $searchData = $this->getSearchData();

        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getList',
            ],
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('items', $result);
        $this->assertCount(2, $result['items']);
        $this->assertArrayHasKey('search_criteria', $result);
        $this->assertEquals($searchData, $result['search_criteria']);
        $this->assertEquals('100000002', $result['items'][0]['increment_id']);
        $this->assertEquals('100000001', $result['items'][1]['increment_id']);
    }

    /**
     * Get search data for request.
     *
     * @return array
     */
    private function getSearchData(): array
    {
        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->objectManager->get(
            SortOrderBuilder::class
        );
        /** @var $searchCriteriaBuilder  SearchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(
            SearchCriteriaBuilder::class
        );

        /** @var $filterBuilder  FilterBuilder */
        $filterBuilder = $this->objectManager->create(
            FilterBuilder::class
        );
        $filter1 = $filterBuilder
            ->setField('status')
            ->setValue('processing')
            ->setConditionType('eq')
            ->create();
        $filter2 = $filterBuilder
            ->setField('state')
            ->setValue(Order::STATE_NEW)
            ->setConditionType('eq')
            ->create();
        $filter3 = $filterBuilder
            ->setField('increment_id')
            ->setValue('100000001')
            ->setConditionType('eq')
            ->create();
        $sortOrder = $sortOrderBuilder->setField('grand_total')
            ->setDirection('DESC')
            ->create();
        $searchCriteriaBuilder->addFilters([$filter1]);
        $searchCriteriaBuilder->addFilters([$filter2, $filter3]);
        $searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteriaBuilder->setPageSize(20);
        $searchData = $searchCriteriaBuilder->create()->__toArray();

        return $searchData;
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order_list_with_tax.php
     */
    public function testOrderListExtensionAttributes()
    {
        $searchData = $this->getSearchData();

        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getList',
            ],
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);

        $expectedTax = [
            'code' => 'US-NY-*-Rate 1',
            'type' => 'shipping'
        ];
        $appliedTaxes = $result['items'][0]['extension_attributes']['applied_taxes'];
        $this->assertEquals($expectedTax['code'], $appliedTaxes[0]['code']);
        $appliedTaxes = $result['items'][0]['extension_attributes']['item_applied_taxes'];
        $this->assertEquals($expectedTax['type'], $appliedTaxes[0]['type']);
        $this->assertNotEmpty($appliedTaxes[0]['applied_taxes']);
        $this->assertTrue($result['items'][0]['extension_attributes']['converting_from_quote']);
        $this->assertArrayHasKey('payment_additional_info', $result['items'][0]['extension_attributes']);
        $this->assertNotEmpty($result['items'][0]['extension_attributes']['payment_additional_info']);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogInventory\Api;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class StockItemTest
 */
class StockItemTest extends WebapiAbstract
{
    /**
     * Service name
     */
    const SERVICE_NAME = 'catalogInventoryStockItemApi';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Resource path
     */
    const RESOURCE_GET_PATH = '/V1/stockItems';

    /**
     * Resource path
     */
    const RESOURCE_PUT_PATH = '/V1/products/:productSku/stockItems/:itemId';

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /**
     * @param array $newData
     * @param array $expectedResult
     * @param array $fixtureData
     * @magentoApiDataFixture Magento/Catalog/_files/multiple_products.php
     * @dataProvider saveStockItemBySkuWithWrongInputDataProvider
     */
    public function testStockItemPUTWithWrongInput($newData, $expectedResult, $fixtureData)
    {
        $stockItemOld = $this->getStockItemBySku($fixtureData);
        $productSku = 'simple1';
        $itemId = $stockItemOld['item_id'];

        $resourcePath = str_replace([':productSku', ':itemId'], [$productSku, $itemId], self::RESOURCE_PUT_PATH);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $resourcePath,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => 'catalogInventoryStockRegistryV1',
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogInventoryStockRegistryV1UpdateStockItemBySku',
            ],
        ];

        /** @var StockItemInterfaceFactory $stockItemDetailsDo */
        $stockItemDetailsDo = $this->objectManager
            ->get(StockItemInterfaceFactory::class)
            ->create();
        /** @var DataObjectHelper $dataObjectHelper */
        $dataObjectHelper = $this->objectManager->get(DataObjectHelper::class);
        $dataObjectHelper->populateWithArray(
            $stockItemDetailsDo,
            $newData,
            StockItemInterface::class
        );
        $data = $stockItemDetailsDo->getData();
        $data['show_default_notification_message'] = false;
        $arguments = ['productSku' => $productSku, 'stockItem' => $data];
        $this->assertEquals($stockItemOld['item_id'], $this->_webApiCall($serviceInfo, $arguments));

        /** @var StockItemInterfaceFactory $stockItemFactory */
        $stockItemFactory = $this->objectManager
            ->get(StockItemInterfaceFactory::class);
        $stockItem = $stockItemFactory->create();
        /** @var Item $stockItemResource */
        $stockItemResource = $this->objectManager->get(Item::class);
        $stockItemResource->loadByProductId($stockItem, $stockItemOld['product_id'], $stockItemOld['stock_id']);
        $expectedResult['item_id'] = $stockItem->getItemId();
        $this->assertEquals($expectedResult, array_intersect_key($stockItem->getData(), $expectedResult));
    }

    /**
     * @param array $result
     * @return array
     */
    protected function getStockItemBySku($result)
    {
        $productSku = 'simple1';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_GET_PATH . "/$productSku",
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => 'catalogInventoryStockRegistryV1',
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogInventoryStockRegistryV1GetStockItemBySku',
            ],
        ];
        $arguments = ['productSku' => $productSku];
        $apiResult = $this->_webApiCall($serviceInfo, $arguments);
        $result['item_id'] = $apiResult['item_id'];
        $this->assertEquals($result, array_intersect_key($apiResult, $result), 'The stock data does not match.');
        return $apiResult;
    }

    /**
     * @return array
     */
    public function saveStockItemBySkuWithWrongInputDataProvider()
    {
        return [
            [
                [
                    'item_id' => 222,
                    'product_id' => 222,
                    'stock_id' => 1,
                    'qty' => '111.0000',
                    'min_qty' => '0.0000',
                    'use_config_min_qty' => 1,
                    'is_qty_decimal' => 0,
                    'backorders' => 0,
                    'use_config_backorders' => 1,
                    'min_sale_qty' => '1.0000',
                    'use_config_min_sale_qty' => 1,
                    'max_sale_qty' => '0.0000',
                    'use_config_max_sale_qty' => 1,
                    'is_in_stock' => 1,
                    'low_stock_date' => '',
                    'notify_stock_qty' => null,
                    'use_config_notify_stock_qty' => 1,
                    'manage_stock' => 0,
                    'use_config_manage_stock' => 1,
                    'stock_status_changed_auto' => 0,
                    'use_config_qty_increments' => 1,
                    'qty_increments' => '0.0000',
                    'use_config_enable_qty_inc' => 1,
                    'enable_qty_increments' => 0,
                    'is_decimal_divided' => 0,
                ],
                [
                    'item_id' => '1',
                    'product_id' => '10',
                    'stock_id' => '1',
                    'qty' => '111.0000',
                    'min_qty' => '0.0000',
                    'use_config_min_qty' => '1',
                    'is_qty_decimal' => '0',
                    'backorders' => '0',
                    'use_config_backorders' => '1',
                    'min_sale_qty' => '1.0000',
                    'use_config_min_sale_qty' => '1',
                    'max_sale_qty' => '0.0000',
                    'use_config_max_sale_qty' => '1',
                    'is_in_stock' => '1',
                    'low_stock_date' => null,
                    'notify_stock_qty' => null,
                    'use_config_notify_stock_qty' => '1',
                    'manage_stock' => '0',
                    'use_config_manage_stock' => '1',
                    'stock_status_changed_auto' => '0',
                    'use_config_qty_increments' => '1',
                    'qty_increments' => '0.0000',
                    'use_config_enable_qty_inc' => '1',
                    'enable_qty_increments' => '0',
                    'is_decimal_divided' => '0',
                    'type_id' => 'simple',
                ],
                [
                    'item_id' => 1,
                    'product_id' => 10,
                    'stock_id' => 1,
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'is_qty_decimal' => '',
                    'show_default_notification_message' => '',
                    'use_config_min_qty' => 1,
                    'min_qty' => 0,
                    'use_config_min_sale_qty' => 1,
                    'min_sale_qty' => 1,
                    'use_config_max_sale_qty' => 1,
                    'max_sale_qty' => 10000,
                    'use_config_backorders' => 1,
                    'backorders' => 0,
                    'use_config_notify_stock_qty' => 1,
                    'notify_stock_qty' => 1,
                    'use_config_qty_increments' => 1,
                    'qty_increments' => 0,
                    'use_config_enable_qty_inc' => 1,
                    'enable_qty_increments' => '',
                    'use_config_manage_stock' => 1,
                    'manage_stock' => 1,
                    'low_stock_date' => '',
                    'is_decimal_divided' => '',
                    'stock_status_changed_auto' => 0,
                ],
            ],
        ];
    }

    /**
     * Execute per test initialization
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}

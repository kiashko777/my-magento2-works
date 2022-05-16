<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Ui\DataProvider\Product;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityAndStockStatusFieldToCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Quantity and stock status test
 */
class QuantityAndStockStatusTest extends TestCase
{
    /**
     * @var string
     */
    private static $quantityAndStockStatus = 'quantity_and_stock_status';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Test product stock status in the products grid column
     *
     * @magentoDataFixture Magento/Catalog/_files/quantity_and_stock_status_attribute_used_in_grid.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testProductStockStatus()
    {
        /** @var StockItemRepository $stockItemRepository */
        $stockItemRepository = $this->objectManager->create(StockItemRepository::class);

        /** @var StockRegistryInterface $stockRegistry */
        $stockRegistry = $this->objectManager->create(StockRegistryInterface::class);

        $stockItem = $stockRegistry->getStockItemBySku('simple');
        $stockItem->setIsInStock(false);
        $stockItemRepository->save($stockItem);
        $savedStockStatus = (int)$stockItem->getIsInStock();

        $dataProvider = $this->objectManager->create(
            ProductDataProvider::class,
            [
                'name' => 'product_listing_data_source',
                'primaryFieldName' => 'entity_id',
                'requestFieldName' => 'id',
                'addFieldStrategies' => [
                    'quantity_and_stock_status' =>
                        $this->objectManager->get(AddQuantityAndStockStatusFieldToCollection::class)
                ]
            ]
        );

        $dataProvider->addField(self::$quantityAndStockStatus);
        $data = $dataProvider->getData();
        $dataProviderStockStatus = $data['items'][0][self::$quantityAndStockStatus];

        $this->assertEquals($dataProviderStockStatus, $savedStockStatus);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}

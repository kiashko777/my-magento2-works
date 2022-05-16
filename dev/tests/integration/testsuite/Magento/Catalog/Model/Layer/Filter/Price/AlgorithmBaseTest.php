<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Layer\Filter\Price;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Category;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\Price;
use Magento\CatalogSearch\Model\Price\Interval;
use Magento\CatalogSearch\Model\Price\IntervalFactory;
use Magento\Elasticsearch\SearchAdapter\DocumentFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Search\Dynamic\Algorithm;
use Magento\Framework\Search\EntityMetadata;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/Price/_files/products_base.php
 * @magentoDbIsolation disabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AlgorithmBaseTest extends TestCase
{
    /**
     * Layer model
     *
     * @var Layer
     */
    protected $_layer;

    /**
     * Price filter model
     *
     * @var \Magento\Catalog\Model\Layer\Filter\Price
     */
    protected $_filter;

    /**
     * @var Price
     */
    protected $priceResource;

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @dataProvider pricesSegmentationDataProvider
     * @param $categoryId
     * @param array $entityIds
     * @param array $intervalItems
     * @covers       \Magento\Framework\Search\Dynamic\Algorithm::calculateSeparators
     */
    public function testPricesSegmentation($categoryId, array $entityIds, array $intervalItems)
    {
        $this->markTestSkipped('MC-33826:'
            . 'Stabilize skipped test cases for Integration AlgorithmBaseTest with elasticsearch');
        $objectManager = Bootstrap::getObjectManager();
        $layer = $objectManager->create(Category::class);

        /** @var EntityMetadata $entityMetadata */
        $entityMetadata = $objectManager->create(EntityMetadata::class, ['entityId' => 'id']);
        $idKey = $entityMetadata->getEntityId();

        // this class has been removed
        /** @var DocumentFactory $documentFactory */
        $documentFactory = $objectManager->create(
            DocumentFactory::class,
            ['entityMetadata' => $entityMetadata]
        );

        /** @var Document[] $documents */
        $documents = [];
        foreach ($entityIds as $entityId) {
            $rawDocument = [
                $idKey => $entityId,
                'score' => 1,
            ];
            $documents[] = $documentFactory->create($rawDocument);
        }
        /** @var IntervalFactory $intervalFactory */
        $intervalFactory = $objectManager->create(
            IntervalFactory::class
        );
        /** @var Interval $interval */
        $interval = $intervalFactory->create();

        /** @var Algorithm $model */
        $model = $objectManager->create(Algorithm::class);

        $layer->setCurrentCategory($categoryId);
        $collection = $layer->getProductCollection();

        $memoryUsedBefore = memory_get_usage();
        $model->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getPricesCount()
        );

        $items = $model->calculateSeparators($interval);
        $this->assertEquals($intervalItems, $items);

        for ($i = 0, $count = count($intervalItems); $i < $count; ++$i) {
            $this->assertIsArray($items[$i]);
            $this->assertEquals($intervalItems[$i]['from'], $items[$i]['from']);
            $this->assertEquals($intervalItems[$i]['to'], $items[$i]['to']);
            $this->assertEquals($intervalItems[$i]['count'], $items[$i]['count']);
        }

        // Algorithm should use less than 10M
        $this->assertLessThan(10 * 1024 * 1024, memory_get_usage() - $memoryUsedBefore);
    }

    /**
     * @return array
     */
    public function pricesSegmentationDataProvider()
    {
        $testCases = include __DIR__ . '/_files/_algorithm_base_data.php';
        $testCasesNew = $this->getUnSkippedTestCases($testCases);
        $result = [];
        foreach ($testCasesNew as $index => $testCase) {
            $result[] = [
                $index + 4, //category id
                $testCase[1],
                $testCase[2],
            ];
        }
        return $result;
    }

    /**
     * Get unSkipped test cases from dataProvider
     *
     * @param array $testCases
     * @return array
     */
    private function getUnSkippedTestCases(array $testCases): array
    {
        // TO DO UnSkip skipped test cases and remove this function
        $SkippedTestCases = [];
        $UnSkippedTestCases = [];
        foreach ($testCases as $testCase) {
            if (array_key_exists('incomplete_reason', $testCase)) {
                if ($testCase['incomplete_reason'] === " ") {
                    $UnSkippedTestCases [] = $testCase;
                } else {
                    if ($testCase['incomplete_reason'] != " ") {
                        $SkippedTestCases [] = $testCase;
                    }
                }
            }
        }
        return $UnSkippedTestCases;
    }
}

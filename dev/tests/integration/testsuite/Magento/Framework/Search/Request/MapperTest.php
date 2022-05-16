<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Search\Request;

use Magento\Framework\Search\Request\Aggregation\Metric;
use Magento\Framework\Search\Request\Aggregation\Range;
use Magento\Framework\Search\Request\Aggregation\RangeBucket;
use Magento\Framework\Search\Request\Aggregation\TermBucket;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    /**
     * @var Mapper
     */
    protected $mapper;

    public function testGet()
    {
        $this->assertInstanceOf(
            QueryInterface::class,
            $this->mapper->getRootQuery()
        );
    }

    public function testGetBuckets()
    {
        $buckets = $this->mapper->getBuckets();
        $this->assertCount(2, $buckets);

        $this->assertInstanceOf(TermBucket::class, $buckets[0]);
        $this->assertEquals('category_bucket', $buckets[0]->getName());
        $this->assertEquals('category', $buckets[0]->getField());
        $this->assertEquals(BucketInterface::TYPE_TERM, $buckets[0]->getType());
        $metrics = $buckets[0]->getMetrics();
        $this->assertInstanceOf(Metric::class, $metrics[0]);

        $this->assertInstanceOf(RangeBucket::class, $buckets[1]);
        $this->assertEquals('price_bucket', $buckets[1]->getName());
        $this->assertEquals('price', $buckets[1]->getField());
        $this->assertEquals(BucketInterface::TYPE_RANGE, $buckets[1]->getType());
        $metrics = $buckets[1]->getMetrics();
        $ranges = $buckets[1]->getRanges();
        $this->assertInstanceOf(Metric::class, $metrics[0]);
        $this->assertInstanceOf(Range::class, $ranges[0]);
    }

    protected function setUp(): void
    {
        $config = include __DIR__ . '/../_files/search_request_config.php';
        $request = reset($config);
        /** @var Mapper $mapper */
        $this->mapper = Bootstrap::getObjectManager()
            ->create(
                Mapper::class,
                [
                    'queries' => $request['queries'],
                    'rootQueryName' => 'suggested_search_container',
                    'filters' => $request['filters'],
                    'aggregations' => $request['aggregations'],
                ]
            );
    }
}

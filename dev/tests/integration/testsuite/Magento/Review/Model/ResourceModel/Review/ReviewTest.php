<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Model\ResourceModel\Review;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Review\Model\ResourceModel\Review;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class ReviewTest
 */
class ReviewTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Review
     */
    protected $reviewResource;

    /**
     * @var Collection
     */
    protected $reviewCollection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @magentoDataFixture Magento/Review/_files/customer_review_with_rating.php
     */
    public function testAggregate()
    {
        $rating = $this->reviewCollection->getFirstItem();
        $this->reviewResource->aggregate($rating);

        $select = $this->connection->select()->from($this->resource->getTableName('review_entity_summary'));
        $result = $this->connection->fetchRow($select);

        $this->assertEquals(1, $result['reviews_count']);
        $this->assertEquals(40, $result['rating_summary']);
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->resource = $this->objectManager->get(ResourceConnection::class);
        $this->connection = $this->resource->getConnection();
        $this->reviewCollection = $this->objectManager->create(
            Collection::class
        );
        $this->reviewResource = $this->objectManager->create(Review::class);
    }
}

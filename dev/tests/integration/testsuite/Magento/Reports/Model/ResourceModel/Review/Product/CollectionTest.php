<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Model\ResourceModel\Review\Product;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private $_collection;

    public function testGetSelect()
    {
        $select = (string)$this->_collection->getSelect();
        $search = '/SUM\(table_rating.percent\)\/COUNT\(table_rating.rating_id\) AS `avg_rating`'
            . '[\s\S]+SUM\(table_rating.percent_approved\)\/COUNT\(table_rating.rating_id\) AS `avg_rating_approved`'
            . '[\s\S]+LEFT JOIN `.*rating_option_vote_aggregated` AS `table_rating`/';

        $this->assertMatchesRegularExpression($search, $select);
    }

    protected function setUp(): void
    {
        $this->_collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Review\Model\ResourceModel\Review\Product;

use Magento\Review\Model\Review;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Tests some functionality of the Products Review collection
 */
class CollectionTest extends TestCase
{
    /**
     * @param string $status
     * @param int $expectedCount
     * @param string $sortAttribute
     * @param string $dir
     * @param callable $assertion
     * @dataProvider sortOrderAssertionsDataProvider
     * @magentoDataFixture Magento/Review/_files/different_reviews.php
     */
    public function testGetResultingIds(
        ?int     $status,
        int      $expectedCount,
        string   $sortAttribute,
        string   $dir,
        callable $assertion
    )
    {
        /**
         * @var $collection Collection
         */
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        if ($status) {
            $collection->addStatusFilter($status);
        }
        $collection->setOrder($sortAttribute, $dir);
        $actual = $collection->getResultingIds();
        $this->assertCount($expectedCount, $actual);
        $assertion($actual);
    }

    /**
     * @return array
     */
    public function sortOrderAssertionsDataProvider(): array
    {
        return [
            [
                Review::STATUS_APPROVED,
                2,
                'rt.review_id',
                'DESC',
                function (array $actual): void {
                    $this->assertLessThan($actual[0], $actual[1]);
                }
            ],
            [
                Review::STATUS_APPROVED,
                2,
                'rt.review_id',
                'ASC',
                function (array $actual): void {
                    $this->assertLessThan($actual[1], $actual[0]);
                }
            ],
            [
                Review::STATUS_APPROVED,
                2,
                'rt.created_at',
                'ASC',
                function (array $actual): void {
                    $this->assertLessThan($actual[1], $actual[0]);
                }
            ],
            [
                null,
                3,
                'rt.review_id',
                'ASC',
                function (array $actual): void {
                    $this->assertLessThan($actual[1], $actual[0]);
                }
            ]
        ];
    }
}

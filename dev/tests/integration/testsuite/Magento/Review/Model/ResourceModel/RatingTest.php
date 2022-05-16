<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Model\ResourceModel;

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class RatingTest
 */
class RatingTest extends TestCase
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @magentoDbIsolation enabled
     */
    public function testRatingLoad()
    {
        $rating = Bootstrap::getObjectManager()->create(
            \Magento\Review\Model\Rating::class
        );
        $rating->load($this->id);
        $this->assertEquals('Test Rating', $rating->getRatingCode());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testRatingEdit()
    {
        $rating = Bootstrap::getObjectManager()->create(
            \Magento\Review\Model\Rating::class
        );
        $rating->load($this->id);
        $this->assertEquals('Test Rating', $rating->getRatingCode());
        $storeId = Bootstrap::getObjectManager()
            ->get(StoreManagerInterface::class)
            ->getStore()->getId();
        $rating->setRatingCode('Test Rating Edited');
        $rating->setRatingCodes([$storeId => 'Test Rating Edited']);
        $rating->save();

        $this->assertEquals('Test Rating Edited', $rating->getRatingCode());
        $this->assertEquals([$storeId => 'Test Rating Edited'], $rating->getRatingCodes());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testRatingSaveWithError()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Rolled back transaction has not been completed correctly');
        $rating = Bootstrap::getObjectManager()->create(
            \Magento\Review\Model\Rating::class
        );
        $rating->load($this->id);
        $rating->setRatingCodes([222 => 'Test Rating Edited']);
        $rating->save();
    }

    /**
     * @magentoDbIsolation enabled
     */
    protected function setUp(): void
    {
        $storeId = Bootstrap::getObjectManager()
            ->get(StoreManagerInterface::class)
            ->getStore()->getId();

        $rating = Bootstrap::getObjectManager()->create(
            \Magento\Review\Model\Rating::class
        );
        $rating->setData([
            'rating_code' => 'Test Rating',
            'position' => 0,
            'is_active' => true,
            'entity_id' => 1
        ]);
        $rating->setRatingCodes([$storeId => 'Test Rating']);
        $rating->setStores([$storeId]);
        $rating->save();
        $this->id = $rating->getId();
    }
}

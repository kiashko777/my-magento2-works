<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CheckoutAgreements\Model\ResourceModel\Grid;

use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Grid\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Check data in collection
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection;
     */
    private $collection;

    /**
     * Check that collection is filterable by store
     *
     * @magentoDataFixture Magento/CheckoutAgreements/_files/multi_agreements_active_with_text.php
     */
    public function testAddStoresToFilter(): void
    {
        $collectionSize = $this->collection->addStoreFilter(1)
            ->load(false, false)
            ->getSize();
        $this->assertEquals(2, $collectionSize);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->collection = Bootstrap::getObjectManager()
            ->create(Collection::class);
    }
}

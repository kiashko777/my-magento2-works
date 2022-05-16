<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Block\Adminhtml\Shopcart\Product;

use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Reports\Block\Adminhtml\Shopcart\GridTestAbstract;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Magento\Reports\Block\Adminhtml\Shopcart\Products\Grid
 *
 * @magentoAppArea Adminhtml
 * @magentoDataFixture Magento/Sales/_files/quote.php
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class GridTest extends GridTestAbstract
{
    /**
     * @return void
     */
    public function testGridContent()
    {
        $this->markTestSkipped('MC-40448: Products\GridTest failure on 2.4-develop');
        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Grid $grid */
        $grid = $layout->createBlock(Grid::class);
        $result = $grid->getPreparedCollection();

        $this->assertCount(1, $result->getItems());
        /** @var Item $quoteItem */
        $quoteItem = $result->getFirstItem();
        $this->assertInstanceOf(Item::class, $quoteItem);

        $this->assertGreaterThan(0, (int)$quoteItem->getProductId());
        $this->assertEquals('Simple Products', $quoteItem->getName());
    }
}

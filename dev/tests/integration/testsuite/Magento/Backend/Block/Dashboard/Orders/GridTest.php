<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Dashboard\Orders;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    /**
     * @var Grid
     */
    private $block;

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testGetPreparedCollection()
    {
        $collection = $this->block->getPreparedCollection();
        foreach ($collection->getItems() as $item) {
            if ($item->getIncrementId() == '100000001') {
                $this->assertEquals('firstname lastname', $item->getCustomer());
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $block = $this->createMock(Grid::class);
        $layout = $this->createMock(LayoutInterface::class);
        $layout->expects($this->atLeastOnce())->method('getChildName')->willReturn('test');
        $layout->expects($this->atLeastOnce())->method('getBlock')->willReturn($block);
        $context = $objectManager->create(Context::class, ['layout' => $layout]);

        $this->block = $objectManager->create(Grid::class, ['context' => $context]);
    }
}

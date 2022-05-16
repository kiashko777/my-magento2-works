<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Controller\Adminhtml\Order;

use Magento\CatalogInventory\Api\StockIndexInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class CreditmemoTest extends AbstractBackendController
{
    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/auto_return 1
     * @magentoDataFixture Magento/Sales/_files/order_info.php
     */
    public function testAddCommentAction()
    {
        $this->markTestIncomplete('https://github.com/magento-engcom/msi/issues/393');
        $objectManager = Bootstrap::getObjectManager();
        /** @var StockIndexInterface $stockIndex */
        $stockIndex = $objectManager->get(StockIndexInterface::class);
        $stockIndex->rebuild(1, 1);

        /** @var StockStateInterface $stockState */
        $stockState = $objectManager->create(StockStateInterface::class);
        $this->assertEquals(95, $stockState->getStockQty(1, 1));

        /** @var Order $order */
        $order = $objectManager->create(Order::class);
        $order->load('100000001', 'increment_id');
        $items = $order->getCreditmemosCollection()->getItems();
        $creditmemo = array_shift($items);
        $comment = 'Test Comment 02';
        $this->getRequest()->setParam('creditmemo_id', $creditmemo->getId());
        $this->getRequest()->setPostValue('comment', ['comment' => $comment]);
        $this->dispatch('backend/sales/order_creditmemo/addComment/id/' . $creditmemo->getId());
        $html = $this->getResponse()->getBody();
        $this->assertContains($comment, $html);

        /** @var StockStateInterface $stockState */
        $stockState = $objectManager->create(StockStateInterface::class);
        $this->assertEquals(95, $stockState->getStockQty(1, 1));
    }
}

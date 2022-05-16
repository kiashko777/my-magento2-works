<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Block\Order;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Comment\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CommentsTest extends TestCase
{
    /**
     * @var Comments
     */
    protected $_block;

    /**
     * @param string $commentedEntity
     * @param string $expectedClass
     * @dataProvider getCommentsDataProvider
     */
    public function testGetComments($commentedEntity, $expectedClass)
    {
        $commentedEntity = Bootstrap::getObjectManager()->create($commentedEntity);
        $this->_block->setEntity($commentedEntity);
        $comments = $this->_block->getComments();
        $this->assertInstanceOf($expectedClass, $comments);
    }

    /**
     * @return array
     */
    public function getCommentsDataProvider()
    {
        return [
            [
                \Magento\Sales\Model\Order\Invoice::class,
                \Magento\Sales\Model\ResourceModel\Order\Invoice\Comment\Collection::class,
            ],
            [
                \Magento\Sales\Model\Order\Creditmemo::class,
                \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\Collection::class
            ],
            [
                Shipment::class,
                Collection::class
            ]
        ];
    }

    /**
     */
    public function testGetCommentsWrongEntityException()
    {
        $this->expectException(LocalizedException::class);

        $entity = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $this->_block->setEntity($entity);
        $this->_block->getComments();
    }

    protected function setUp(): void
    {
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Comments::class
        );
    }
}

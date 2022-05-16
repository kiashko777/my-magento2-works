<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Newsletter\Block\Adminhtml\Subscriber;

use Magento\Backend\Block\Widget\Grid\Massaction;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @magentoDbIsolation enabled
 *
 * @see \Magento\Newsletter\Block\Adminhtml\Subscriber\Grid
 */
class GridTest extends TestCase
{
    /**
     * @var null|ObjectManagerInterface
     */
    private $objectManager = null;
    /**
     * @var null|LayoutInterface
     */
    private $layout = null;

    /**
     * Check if mass action block exists.
     */
    public function testMassActionBlockExists()
    {
        $this->assertNotFalse(
            $this->getMassActionBlock(),
            'Mass action block does not exist in the grid, or it name was changed.'
        );
    }

    /**
     * Retrieve mass action block.
     *
     * @return bool|Massaction
     */
    private function getMassActionBlock()
    {
        return $this->layout->getBlock('Adminhtml.newslettrer.subscriber.grid.massaction');
    }

    /**
     * Check if mass action id field is correct.
     */
    public function testMassActionFieldIdIsCorrect()
    {
        $this->assertEquals(
            'subscriber_id',
            $this->getMassActionBlock()->getMassactionIdField(),
            'Mass action id field is incorrect.'
        );
    }

    /**
     * Check if function returns correct result.
     *
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testMassActionBlockContainsCorrectIdList()
    {
        $this->assertEquals(
            implode(',', $this->getAllSubscriberIdList()),
            $this->getMassActionBlock()->getGridIdsJson(),
            'Function returns incorrect result.'
        );
    }

    /**
     * Retrieve list of id of all subscribers.
     *
     * @return array
     */
    private function getAllSubscriberIdList()
    {
        /** @var ResourceConnection $resourceConnection */
        $resourceConnection = $this->objectManager->get(ResourceConnection::class);
        $select = $resourceConnection->getConnection()
            ->select()
            ->from($resourceConnection->getTableName('newsletter_subscriber'))
            ->columns(['subscriber_id' => 'subscriber_id']);

        return $resourceConnection->getConnection()->fetchCol($select);
    }

    /**
     * Set up layout.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();

        $this->layout = $this->objectManager->create(LayoutInterface::class);
        $this->layout->getUpdate()->load('newsletter_subscriber_grid');
        $this->layout->generateXml();
        $this->layout->generateElements();
    }
}

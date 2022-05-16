<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel\Db\Collection;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Zend_Db_Expr;

class AbstractTest extends TestCase
{
    /**
     * @var AbstractCollection
     */
    protected $_model = null;

    public function testGetAllIds()
    {
        $allIds = $this->_model->getAllIds();
        sort($allIds);
        $this->assertEquals(['0', '1'], $allIds);
    }

    public function testGetAllIdsWithBind()
    {
        $this->_model->getSelect()->where('code = :code');
        $this->_model->addBindParam('code', 'admin');
        $this->assertEquals(['0'], $this->_model->getAllIds());
    }

    /**
     * Check add field to select doesn't remove expression field from select.
     *
     * @return void
     */
    public function testAddExpressionFieldToSelectWithAdditionalFields()
    {
        $expectedColumns = ['code', 'test_field'];
        $actualColumns = [];

        $testExpression = new Zend_Db_Expr('(sort_order + group_id)');
        $this->_model->addExpressionFieldToSelect('test_field', $testExpression, ['sort_order', 'group_id']);
        $this->_model->addFieldToSelect('code', 'code');
        $columns = $this->_model->getSelect()->getPart(Select::COLUMNS);
        foreach ($columns as $columnEntry) {
            $actualColumns[] = $columnEntry[2];
        }

        $this->assertEquals($expectedColumns, $actualColumns);
    }

    /**
     * Check add expression field doesn't remove all fields from select.
     *
     * @return void
     */
    public function testAddExpressionFieldToSelectWithoutAdditionalFields()
    {
        $expectedColumns = ['*', 'test_field'];

        $testExpression = new Zend_Db_Expr('(sort_order + group_id)');
        $this->_model->addExpressionFieldToSelect('test_field', $testExpression, ['sort_order', 'group_id']);
        $columns = $this->_model->getSelect()->getPart(Select::COLUMNS);
        $actualColumns = [$columns[0][1], $columns[1][2]];

        $this->assertEquals($expectedColumns, $actualColumns);
    }

    protected function setUp(): void
    {
        $resourceModel = Bootstrap::getObjectManager()
            ->get(ResourceConnection::class);
        $context = Bootstrap::getObjectManager()->create(
            Context::class,
            ['resource' => $resourceModel]
        );

        $resource = $this->getMockForAbstractClass(
            AbstractDb::class,
            [$context],
            '',
            true,
            true,
            true,
            ['getMainTable', 'getIdFieldName']
        );

        $resource->expects(
            $this->any()
        )->method(
            'getMainTable'
        )->willReturn(
            $resource->getTable('store_website')
        );
        $resource->expects($this->any())->method('getIdFieldName')->willReturn('website_id');

        $fetchStrategy = $this->getMockForAbstractClass(
            FetchStrategyInterface::class
        );

        $eventManager = Bootstrap::getObjectManager()->get(
            ManagerInterface::class
        );

        $entityFactory = Bootstrap::getObjectManager()->get(
            EntityFactory::class
        );
        $logger = Bootstrap::getObjectManager()->get(LoggerInterface::class);

        $this->_model = $this->getMockForAbstractClass(
            AbstractCollection::class,
            [$entityFactory, $logger, $fetchStrategy, $eventManager, null, $resource]
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\DB;

use Magento\Store\Model\ResourceModel\Store\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * @var Helper
     */
    protected $_model;

    /**
     * @var Select
     */
    protected $_select;

    public function testPrepareColumnsList()
    {
        $columns = $this->_model->prepareColumnsList($this->_select);
        $this->assertContains('STORE_ID', array_keys($columns));
    }

    public function testAddGroupConcatColumn()
    {
        $select = (string)$this->_model->addGroupConcatColumn($this->_select, 'test_alias', 'store_id');
        $this->assertStringContainsString('GROUP_CONCAT', $select);
        $this->assertStringContainsString('test_alias', $select);
    }

    public function testGetDateDiff()
    {
        $diff = $this->_model->getDateDiff('2011-01-01', '2011-01-01');
        $this->assertInstanceOf('Zend_Db_Expr', $diff);
        $this->assertStringContainsString('TO_DAYS', (string)$diff);
    }

    public function testAddLikeEscape()
    {
        $value = $this->_model->addLikeEscape('test');
        $this->assertInstanceOf('Zend_Db_Expr', $value);
        $this->assertStringContainsString('test', (string)$value);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Helper::class,
            ['modulePrefix' => 'core']
        );
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $this->_select = $collection->getSelect();
    }
}

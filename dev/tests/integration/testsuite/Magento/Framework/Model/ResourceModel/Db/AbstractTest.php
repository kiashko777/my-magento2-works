<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel\Db;

use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class AbstractTest extends TestCase
{
    /**
     * @var AbstractDb
     */
    protected $_model;

    public function testConstruct()
    {
        $resourceProperty = new ReflectionProperty(get_class($this->_model), '_resources');
        $resourceProperty->setAccessible(true);
        $this->assertInstanceOf(
            ResourceConnection::class,
            $resourceProperty->getValue($this->_model)
        );
    }

    public function testSetMainTable()
    {
        $setMainTableMethod = new ReflectionMethod($this->_model, '_setMainTable');
        $setMainTableMethod->setAccessible(true);

        $tableName = $this->_model->getTable('store_website');
        $idFieldName = 'website_id';

        $setMainTableMethod->invoke($this->_model, $tableName);
        $this->assertEquals($tableName, $this->_model->getMainTable());

        $setMainTableMethod->invoke($this->_model, $tableName, $idFieldName);
        $this->assertEquals($tableName, $this->_model->getMainTable());
        $this->assertEquals($idFieldName, $this->_model->getIdFieldName());
    }

    public function testGetTableName()
    {
        $tableNameOrig = 'store_website';
        $tableSuffix = 'suffix';
        $resource = Bootstrap::getObjectManager()->create(
            ResourceConnection::class,
            ['tablePrefix' => 'prefix_']
        );
        $context = Bootstrap::getObjectManager()->create(
            Context::class,
            ['resource' => $resource]
        );

        $model = $this->getMockForAbstractClass(
            AbstractDb::class,
            ['context' => $context]
        );

        $tableName = $model->getTable([$tableNameOrig, $tableSuffix]);
        $this->assertEquals('prefix_store_website_suffix', $tableName);
    }

    protected function setUp(): void
    {
        $resource = Bootstrap::getObjectManager()->get(
            ResourceConnection::class
        );
        $context = Bootstrap::getObjectManager()->create(
            Context::class,
            ['resource' => $resource]
        );
        $this->_model = $this->getMockForAbstractClass(
            AbstractDb::class,
            ['context' => $context]
        );
    }
}

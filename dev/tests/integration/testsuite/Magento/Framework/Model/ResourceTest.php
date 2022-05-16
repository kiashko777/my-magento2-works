<?php
/**
 * Test for \Magento\Framework\Model\ResourceModel
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\ResourceModel\Db\Profiler;
use Magento\TestFramework\Db\Adapter\Mysql;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    /**
     * @var ResourceConnection
     */
    protected $_model;

    public function testGetTableName()
    {
        $tablePrefix = 'prefix_';
        $tableSuffix = 'suffix';
        $tableNameOrig = 'store_website';

        $this->_model = Bootstrap::getObjectManager()->create(
            ResourceConnection::class,
            ['tablePrefix' => 'prefix_']
        );

        $tableName = $this->_model->getTableName([$tableNameOrig, $tableSuffix]);
        $this->assertStringContainsString($tablePrefix, $tableName);
        $this->assertStringContainsString($tableSuffix, $tableName);
        $this->assertStringContainsString($tableNameOrig, $tableName);
    }

    /**
     * Init profiler during creation of DB connect
     * @return void
     */
    public function testProfilerInit()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $objectManager->create(
            Mysql::class,
            [
                'config' => [
                    'profiler' => [
                        'class' => Profiler::class,
                        'enabled' => 'true',
                    ],
                    'username' => 'username',
                    'password' => 'password',
                    'host' => 'host',
                    'type' => 'type',
                    'dbname' => 'dbname',
                ]
            ]
        );

        /** @var Profiler $profiler */
        $profiler = $connection->getProfiler();

        $this->assertInstanceOf(Profiler::class, $profiler);
        $this->assertTrue($profiler->getEnabled());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()
            ->create(ResourceConnection::class);
    }
}

<?php
/**
 * Test for \Magento\Framework\Model\ResourceModel\Db\Profiler
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel\Db;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\DB\Adapter\TableNotFoundException;
use Magento\TestFramework\Db\Adapter\Mysql;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Zend_Db_Adapter_Pdo_Abstract;
use Zend_Db_Profiler;
use Zend_Db_Profiler_Query;

/**
 * Test profiler on database queries
 */
class ProfilerTest extends TestCase
{
    /**
     * @var string
     */
    protected static $_testResourceName = 'testtest_0000_setup';
    /**
     * @var ResourceConnection
     */
    protected $_model;

    /**
     * @inheritdoc
     *
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function setUpBeforeClass(): void
    {
        self::$_testResourceName = 'testtest_' . random_int(1000, 9999) . '_setup';

        \Magento\Framework\Profiler::enable();
    } // phpcs:enable

    /**
     * @inheritdoc
     *
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function tearDownAfterClass(): void
    {
        \Magento\Framework\Profiler::disable();
    } // phpcs:enable

    /**
     * Init profiler during creation of DB connect
     *
     * @param string $selectQuery
     * @param int $queryType
     * @dataProvider profileQueryDataProvider
     */
    public function testProfilerInit($selectQuery, $queryType)
    {
        $connection = $this->_getConnection();

        /** @var ResourceConnection $resource */
        $resource = Bootstrap::getObjectManager()
            ->get(ResourceConnection::class);
        $testTableName = $resource->getTableName('setup_module');
        $selectQuery = sprintf($selectQuery, $testTableName);

        $result = $connection->query($selectQuery);
        if ($queryType == Zend_Db_Profiler::SELECT) {
            $result->fetchAll();
        }

        /** @var Profiler $profiler */
        $profiler = $connection->getProfiler();
        $this->assertInstanceOf(Profiler::class, $profiler);

        $queryProfiles = $profiler->getQueryProfiles($queryType);
        $this->assertCount(1, $queryProfiles);

        /** @var Zend_Db_Profiler_Query $queryProfile */
        $queryProfile = end($queryProfiles);
        $this->assertInstanceOf('Zend_Db_Profiler_Query', $queryProfile);

        $this->assertEquals($selectQuery, $queryProfile->getQuery());
    }

    /**
     * @return Mysql
     */
    protected function _getConnection()
    {
        $objectManager = Bootstrap::getObjectManager();
        $reader = $objectManager->get(DeploymentConfig::class);
        $dbConfig = $reader->getConfigData(ConfigOptionsListConstants::KEY_DB);
        $connectionConfig = $dbConfig['connection']['default'];
        $connectionConfig['profiler'] = [
            'class' => Profiler::class,
            'enabled' => 'true',
        ];

        return $objectManager->create(Mysql::class, ['config' => $connectionConfig]);
    }

    /**
     * @return array
     */
    public function profileQueryDataProvider()
    {
        return [
            ["SELECT * FROM %s", \Magento\Framework\DB\Profiler::SELECT],
            [
                "INSERT INTO %s (module, schema_version, data_version) " .
                "VALUES ('" .
                self::$_testResourceName .
                "', '1.1', '1.1')",
                \Magento\Framework\DB\Profiler::INSERT
            ],
            [
                "UPDATE %s SET schema_version = '1.2' WHERE module = '" . self::$_testResourceName . "'",
                \Magento\Framework\DB\Profiler::UPDATE
            ],
            [
                "DELETE FROM %s WHERE module = '" . self::$_testResourceName . "'",
                \Magento\Framework\DB\Profiler::DELETE
            ]
        ];
    }

    /**
     * Test correct event starting and stopping in magento profile during SQL query fail
     */
    public function testProfilerDuringSqlException()
    {
        /** @var Zend_Db_Adapter_Pdo_Abstract $connection */
        $connection = $this->_getConnection();

        try {
            $connection->select()->from('unknown_table')->query()->fetch();
        } catch (TableNotFoundException $exception) {
            $this->assertNotEmpty($exception);
        }

        if (!isset($exception)) {
            $this->fail("Expected exception wasn't thrown!");
        }

        /** @var ResourceConnection $resource */
        $resource = Bootstrap::getObjectManager()
            ->get(ResourceConnection::class);
        $testTableName = $resource->getTableName('setup_module');
        $connection->select()->from($testTableName)->query()->fetch();

        /** @var Profiler $profiler */
        $profiler = $connection->getProfiler();
        $this->assertInstanceOf(Profiler::class, $profiler);

        $queryProfiles = $profiler->getQueryProfiles(\Magento\Framework\DB\Profiler::SELECT);
        $this->assertCount(2, $queryProfiles);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()
            ->create(ResourceConnection::class);
    }
}

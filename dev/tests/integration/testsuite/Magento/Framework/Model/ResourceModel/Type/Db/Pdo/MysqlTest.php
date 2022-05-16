<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel\Type\Db\Pdo;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\DB\Profiler;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase
{
    public function testGetConnection()
    {
        $db = Bootstrap::getInstance()->getBootstrap()->getApplication()->getDbInstance();
        $config = [
            'profiler' => [
                'class' => Profiler::class,
                'enabled' => true,
            ],
            'type' => 'pdo_mysql',
            'host' => $db->getHost(),
            'username' => $db->getUser(),
            'password' => $db->getPassword(),
            'dbname' => $db->getSchema(),
            'active' => true,
        ];
        /** @var Mysql $object */
        $object = Bootstrap::getObjectManager()->create(
            Mysql::class,
            ['config' => $config]
        );

        $connection = $object->getConnection(
            Bootstrap::getObjectManager()->get(
                LoggerInterface::class
            )
        );
        $this->assertInstanceOf(\Magento\Framework\DB\Adapter\Pdo\Mysql::class, $connection);
        $profiler = $connection->getProfiler();
        $this->assertInstanceOf(Profiler::class, $profiler);
    }
}

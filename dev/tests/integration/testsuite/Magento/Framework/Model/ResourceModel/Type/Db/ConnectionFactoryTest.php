<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel\Type\Db;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    /**
     * @var ConnectionFactory
     */
    private $model;

    public function testCreate()
    {
        $dbInstance = Bootstrap::getInstance()
            ->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        $dbConfig = [
            'host' => $dbInstance->getHost(),
            'username' => $dbInstance->getUser(),
            'password' => $dbInstance->getPassword(),
            'dbname' => $dbInstance->getSchema(),
            'active' => true,
        ];
        $connection = $this->model->create($dbConfig);
        $this->assertInstanceOf(AdapterInterface::class, $connection);
    }

    protected function setUp(): void
    {
        $this->model = new ConnectionFactory(
            Bootstrap::getObjectManager()
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\ResourceConnection;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\LoggerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

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
        $this->assertClassHasAttribute('logger', get_class($connection));
        $object = new ReflectionClass(get_class($connection));
        $attribute = $object->getProperty('logger');
        $attribute->setAccessible(true);
        $propertyObject = $attribute->getValue($connection);
        $attribute->setAccessible(false);
        $this->assertInstanceOf(LoggerInterface::class, $propertyObject);
    }

    protected function setUp(): void
    {
        $this->model = new ConnectionFactory(
            Bootstrap::getObjectManager()
        );
    }
}

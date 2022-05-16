<?php
/**
 * \Magento\Payment\Model\Config
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Model;

use Magento\Framework\App\Cache;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Payment\Model\Config\Data;
use Magento\Payment\Model\Config\Reader;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model = null;

    /**
     * @covers \Magento\Payment\Model\Config::getActiveMethods
     */
    public function testGetActiveMethods()
    {
        $paymentMethods = $this->model->getActiveMethods();
        static::assertNotEmpty($paymentMethods);

        /** @var MethodInterface $method */
        foreach ($paymentMethods as $method) {
            static::assertNotEmpty($method->getCode());
            static::assertTrue($method->isActive());
            static::assertEquals(0, $method->getStore());
        }
    }

    public function testGetCcTypes()
    {
        $expected = ['AE' => 'American Express', 'SM' => 'Switch/Maestro', 'SO' => 'Solo'];
        $ccTypes = $this->model->getCcTypes();
        $this->assertEquals($expected, $ccTypes);
    }

    public function testGetGroups()
    {
        $expected = ['any_payment' => 'Any Payment Methods', 'offline' => 'Offline Payment Methods'];
        $groups = $this->model->getGroups();
        $this->assertEquals($expected, $groups);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $cache Cache */
        $cache = $objectManager->create(Cache::class);
        $cache->clean();
        $fileResolverMock = $this->getMockBuilder(
            FileResolverInterface::class
        )->disableOriginalConstructor()->getMock();
        $fileList = [
            file_get_contents(__DIR__ . '/_files/payment.xml'),
            file_get_contents(__DIR__ . '/_files/payment2.xml'),
        ];
        $fileResolverMock->expects($this->any())->method('get')->willReturn($fileList);
        $reader = $objectManager->create(
            Reader::class,
            ['fileResolver' => $fileResolverMock]
        );
        $data = $objectManager->create(Data::class, ['reader' => $reader]);
        $this->model = $objectManager->create(Config::class, ['dataStorage' => $data]);
    }

    protected function tearDown(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $cache Cache */
        $cache = $objectManager->create(Cache::class);
        $cache->clean();
    }
}

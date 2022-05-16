<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Test\Annotation;

use Magento\TestFramework\Annotation\AdminConfigFixture;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Test class for \Magento\TestFramework\Annotation\AdminConfigFixture.
 */
class AdminConfigFixtureTest extends TestCase
{
    /**
     * @var AdminConfigFixture|MockObject
     */
    protected $object;

    /**
     * @magentoAdminConfigFixture any_config some_value
     *
     * @return void
     */
    public function testConfig(): void
    {
        $this->createResolverMock();
        $this->object->expects(
            $this->at(0)
        )->method(
            '_getConfigValue'
        )->with(
            'any_config'
        )->willReturn(
            'some_value'
        );
        $this->object->expects($this->at(1))->method('_setConfigValue')->with('any_config', 'some_value');
        $this->object->startTest($this);

        $this->object->expects($this->once())->method('_setConfigValue')->with('any_config', 'some_value');
        $this->object->endTest($this);
    }

    /**
     * Create mock for Resolver object
     *
     * @return void
     */
    private function createResolverMock(): void
    {
        $mock = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->setMethods(['applyConfigFixtures'])
            ->getMock();
        $mock->method('applyConfigFixtures')
            ->willReturn($this->getAnnotations()['method'][$this->object::ANNOTATION]);
        $reflection = new ReflectionClass(Resolver::class);
        $reflectionProperty = $reflection->getProperty('instance');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue(Resolver::class, $mock);
    }

    /**
     * @return void
     */
    public function testInitStoreAfterOfScope(): void
    {
        $this->object->expects($this->never())->method('_getConfigValue');
        $this->object->expects($this->never())->method('_setConfigValue');
        $this->object->initStoreAfter();
    }

    /**
     * @magentoAdminConfigFixture any_config some_value
     *
     * @return void
     */
    public function testInitStoreAfter(): void
    {
        $this->createResolverMock();
        $this->object->startTest($this);
        $this->object->expects(
            $this->at(0)
        )->method(
            '_getConfigValue'
        )->with(
            'any_config'
        )->willReturn(
            'some_value'
        );
        $this->object->expects($this->at(1))->method('_setConfigValue')->with('any_config', 'some_value');
        $this->object->initStoreAfter();
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->object = $this->createPartialMock(
            AdminConfigFixture::class,
            ['_getConfigValue', '_setConfigValue']
        );
    }
}

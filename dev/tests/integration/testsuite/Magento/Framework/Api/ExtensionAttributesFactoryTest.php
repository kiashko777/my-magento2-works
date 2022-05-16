<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Api;

use LogicException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleExtensionAttributes\Api\Data\FakeRegionExtension;
use Magento\TestModuleExtensionAttributes\Model\Data\FakeExtensibleOne;
use Magento\TestModuleExtensionAttributes\Model\Data\FakeExtensibleTwo;
use Magento\TestModuleExtensionAttributes\Model\Data\FakeRegion;
use PHPUnit\Framework\TestCase;

class ExtensionAttributesFactoryTest extends TestCase
{
    /** @var ExtensionAttributesFactory */
    private $factory;

    /**
     */
    public function testCreateThrowExceptionIfInterfaceNotImplemented()
    {
        $this->expectException(LogicException::class);

        $this->factory->create(ExtensionAttributesFactoryTest::class);
    }

    /**
     */
    public function testCreateThrowExceptionIfInterfaceNotOverridden()
    {
        $this->expectException(LogicException::class);

        $this->factory->create(FakeExtensibleOne::class);
    }

    /**
     */
    public function testCreateThrowExceptionIfReturnIsIncorrect()
    {
        $this->expectException(LogicException::class);

        $this->factory->create(FakeExtensibleTwo::class);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(
            FakeRegionExtension::class,
            $this->factory->create(FakeRegion::class)
        );
    }

    public function testCreateWithLogicException()
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage(
            "Class 'Magento\\Framework\\Api\\ExtensionAttributesFactoryTest' must implement an interface, "
            . "which extends from 'Magento\\Framework\\Api\\ExtensibleDataInterface'"
        );
        $this->factory->create(get_class($this));
    }

    protected function setUp(): void
    {
        /** @var ObjectManagerInterface */
        $objectManager = Bootstrap::getObjectManager();

        $this->factory = $objectManager->create(
            ExtensionAttributesFactory::class,
            ['objectManager' => $objectManager]
        );
    }
}

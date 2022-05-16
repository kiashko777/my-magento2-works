<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Api;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleExtensionAttributes\Api\Data\FakeAddressInterface;
use Magento\TestModuleExtensionAttributes\Api\Data\FakeRegionExtension;
use Magento\TestModuleExtensionAttributes\Api\Data\FakeRegionInterface;
use Magento\TestModuleExtensionAttributes\Model\Data\FakeRegionExtensionInterface;
use Magento\TestModuleExtensionAttributes\Model\Data\FakeRegionFactory;
use Magento\TestModuleExtensionAttributes\Model\FakeAddress;
use Magento\TestModuleExtensionAttributes\Model\FakeRegion;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Framework\Api\AbstractExtensibleObject
 */
class AbstractExtensibleObjectTest extends TestCase
{
    /** @var ObjectManagerInterface */
    private $_objectManager;

    /**
     * Test setExtensionAttributes and getExtensionAttributes for \Magento\Framework\Api\AbstractExtensibleObject
     *
     * @param array $expectedDataBefore
     * @param array $expectedDataAfter
     * @dataProvider extensionAttributesDataProvider
     */
    public function testExtensionAttributes($expectedDataBefore, $expectedDataAfter)
    {
        /** @var ExtensionAttributesFactory $regionExtensionFactory */
        $regionExtensionFactory = $this->_objectManager->get(ExtensionAttributesFactory::class);
        /** @var FakeRegionFactory $regionFactory */
        $regionFactory = $this->_objectManager->get(FakeRegionFactory::class);

        /** @var \Magento\TestModuleExtensionAttributes\Model\Data\FakeRegion $region */
        $region = $regionFactory->create();

        $regionCode = 'test_code';
        /** @var FakeRegionExtensionInterface $regionExtension */
        $regionExtension = $regionExtensionFactory->create(
            \Magento\TestModuleExtensionAttributes\Model\Data\FakeRegion::class,
            ['data' => $expectedDataBefore]
        );
        $region->setRegionCode($regionCode)->setExtensionAttributes($regionExtension);
        $this->assertInstanceOf(\Magento\TestModuleExtensionAttributes\Model\Data\FakeRegion::class, $region);

        $extensionAttributes = $region->getExtensionAttributes();
        $this->assertInstanceOf(FakeRegionExtension::class, $extensionAttributes);
        $this->assertEquals($expectedDataBefore, $extensionAttributes->__toArray());
        $this->assertEquals($regionCode, $region->getRegionCode());

        $regionCode = 'changed_test_code';
        $region->setExtensionAttributes(
            $regionExtensionFactory->create(
                \Magento\TestModuleExtensionAttributes\Model\Data\FakeRegion::class,
                ['data' => $expectedDataAfter]
            )
        )->setRegionCode($regionCode); // change $regionCode to test AbstractExtensibleObject::setData
        $extensionAttributes = $region->getExtensionAttributes();
        $this->assertEquals($expectedDataAfter, $extensionAttributes->__toArray());
        $this->assertEquals($regionCode, $region->getRegionCode());
    }

    public function extensionAttributesDataProvider()
    {
        return [
            'boolean' => [
                [true],
                [false]
            ],
            'integer' => [
                [1],
                [2]
            ],
            'string' => [
                ['test'],
                ['test test']
            ],
            'array' => [
                [[1]],
                [[1, 2]]
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_objectManager->configure(
            [
                'preferences' => [
                    FakeAddressInterface::class =>
                        FakeAddress::class,
                    FakeRegionInterface::class =>
                        FakeRegion::class,
                ],
            ]
        );
    }
}

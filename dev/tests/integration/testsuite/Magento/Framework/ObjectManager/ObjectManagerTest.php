<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager;

use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\ObjectManager\Config\Config;
use Magento\Framework\ObjectManager\TestAsset\Basic;
use Magento\Framework\ObjectManager\TestAsset\BasicInjection;
use Magento\Framework\ObjectManager\TestAsset\ConstructorEightArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorFiveArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorFourArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorNineArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorNoArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorOneArgument;
use Magento\Framework\ObjectManager\TestAsset\ConstructorSevenArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorSixArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorTenArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorThreeArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorTwoArguments;
use Magento\Framework\ObjectManager\TestAsset\ConstructorWithTypeError;
use Magento\Framework\ObjectManager\TestAsset\InterfaceImplementation;
use Magento\Framework\ObjectManager\TestAsset\InterfaceInjection;
use Magento\Framework\ObjectManager\TestAsset\TestAssetInterface;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

class ObjectManagerTest extends TestCase
{
    /**#@+
     * Test class with type error
     */
    const TEST_CLASS_WITH_TYPE_ERROR = ConstructorWithTypeError::class;

    /**#@+
     * Test classes for basic instantiation
     */
    const TEST_CLASS = Basic::class;

    const TEST_CLASS_INJECTION = BasicInjection::class;

    /**#@-*/

    /**#@+
     * Test classes and interface to test preferences
     */
    const TEST_INTERFACE = TestAssetInterface::class;

    const TEST_INTERFACE_IMPLEMENTATION = InterfaceImplementation::class;

    const TEST_CLASS_WITH_INTERFACE = InterfaceInjection::class;

    /**#@-*/

    /**
     * @var ObjectManagerInterface
     */
    protected static $_objectManager;

    /**
     * List of classes with different number of arguments
     *
     * @var array
     */
    protected $_numerableClasses = [
        0 => ConstructorNoArguments::class,
        1 => ConstructorOneArgument::class,
        2 => ConstructorTwoArguments::class,
        3 => ConstructorThreeArguments::class,
        4 => ConstructorFourArguments::class,
        5 => ConstructorFiveArguments::class,
        6 => ConstructorSixArguments::class,
        7 => ConstructorSevenArguments::class,
        8 => ConstructorEightArguments::class,
        9 => ConstructorNineArguments::class,
        10 => ConstructorTenArguments::class,
    ];

    /**
     * Names of properties
     *
     * @var array
     */
    protected $_numerableProperties = [
        1 => '_one',
        2 => '_two',
        3 => '_three',
        4 => '_four',
        5 => '_five',
        6 => '_six',
        7 => '_seven',
        8 => '_eight',
        9 => '_nine',
        10 => '_ten',
    ];

    public static function setUpBeforeClass(): void
    {
        $config = new Config();
        $factory = new Factory\Dynamic\Developer($config);

        self::$_objectManager = new ObjectManager($factory, $config);
        self::$_objectManager->configure(
            ['preferences' => [self::TEST_INTERFACE => self::TEST_INTERFACE_IMPLEMENTATION]]
        );
        $factory->setObjectManager(self::$_objectManager);
    }

    public static function tearDownAfterClass(): void
    {
        self::$_objectManager = null;
    }

    /**
     * Data provider for testNewInstance
     *
     * @return array
     */
    public function newInstanceDataProvider()
    {
        $data = [
            'basic model' => [
                '$actualClassName' => self::TEST_CLASS_INJECTION,
                '$properties' => ['_object' => self::TEST_CLASS],
            ],
            'model with interface' => [
                '$actualClassName' => self::TEST_CLASS_WITH_INTERFACE,
                '$properties' => ['_object' => self::TEST_INTERFACE_IMPLEMENTATION],
            ],
        ];

        foreach ($this->_numerableClasses as $number => $className) {
            $properties = [];
            for ($i = 1; $i <= $number; $i++) {
                $propertyName = $this->_numerableProperties[$i];
                $properties[$propertyName] = self::TEST_CLASS;
            }
            $data[$number . ' arguments'] = ['$actualClassName' => $className, '$properties' => $properties];
        }

        return $data;
    }

    /**
     * @param string $actualClassName
     * @param array $properties
     * @param string|null $expectedClassName
     *
     * @dataProvider newInstanceDataProvider
     */
    public function testNewInstance($actualClassName, array $properties = [], $expectedClassName = null)
    {
        if (!$expectedClassName) {
            $expectedClassName = $actualClassName;
        }

        $testObject = self::$_objectManager->create($actualClassName);
        $this->assertInstanceOf($expectedClassName, $testObject);
        $object = new ReflectionClass($actualClassName);
        if ($properties) {
            foreach ($properties as $propertyName => $propertyClass) {
                $this->assertClassHasAttribute($propertyName, $actualClassName);
                $attribute = $object->getProperty($propertyName);
                $attribute->setAccessible(true);
                $propertyObject = $attribute->getValue($testObject);
                $attribute->setAccessible(false);
                $this->assertInstanceOf($propertyClass, $propertyObject);
            }
        }
    }

    /**
     * Test creating an object and passing incorrect type of arguments to the constructor.
     *
     */
    public function testNewInstanceWithTypeError()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error occurred when creating object');

        self::$_objectManager->create(self::TEST_CLASS_WITH_TYPE_ERROR, [
            'testArgument' => new stdClass()
        ]);
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Workaround\Cleanup\TestCaseProperties.
 */

namespace Magento\Test\Workaround\Cleanup;

use Magento\Test\Workaround\Cleanup\TestCasePropertiesTest\DummyTestCase;
use Magento\TestFramework\Workaround\Cleanup\TestCaseProperties;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use ReflectionClass;

class TestCasePropertiesTest extends TestCase
{
    /**
     * @var array
     */
    protected $_fixtureProperties = [
        'testPublic' => ['name' => 'testPublic', 'is_static' => false],
        '_testPrivate' => ['name' => '_testPrivate', 'is_static' => false],
        '_testPropertyBoolean' => ['name' => '_testPropertyBoolean', 'is_static' => false],
        '_testPropertyInteger' => ['name' => '_testPropertyInteger', 'is_static' => false],
        '_testPropertyFloat' => ['name' => '_testPropertyFloat', 'is_static' => false],
        '_testPropertyString' => ['name' => '_testPropertyString', 'is_static' => false],
        '_testPropertyArray' => ['name' => '_testPropertyArray', 'is_static' => false],
        'testPublicStatic' => ['name' => 'testPublicStatic', 'is_static' => true],
        '_testProtectedStatic' => ['name' => '_testProtectedStatic', 'is_static' => true],
        '_testPrivateStatic' => ['name' => '_testPrivateStatic', 'is_static' => true],
    ];

    public function testEndTestSuiteDestruct()
    {
        $phpUnitTestSuite = new TestSuite();
        $phpUnitTestSuite->addTestFile(__DIR__ . '/TestCasePropertiesTest/DummyTestCase.php');
        // Because addTestFile() adds classes from file to tests array, use first testsuite
        /** @var $testSuite TestSuite */
        $testSuite = $phpUnitTestSuite->testAt(0);
        $testSuite->run();
        /** @var $testClass DummyTestCase */
        $testClass = $testSuite->testAt(0);

        $reflectionClass = new ReflectionClass($testClass);
        $classProperties = $reflectionClass->getProperties();
        $fixturePropertiesNames = array_keys($this->_fixtureProperties);
        foreach ($classProperties as $property) {
            if (in_array($property->getName(), $fixturePropertiesNames)) {
                $property->setAccessible(true);
                $value = $property->getValue($testClass);
                $this->assertNotNull($value);
            }
        }

        $clearProperties = new TestCaseProperties();
        $clearProperties->endTestSuite($testSuite);

        foreach ($classProperties as $property) {
            if (in_array($property->getName(), $fixturePropertiesNames)) {
                $property->setAccessible(true);
                $value = $property->getValue($testClass);
                $this->assertNull($value);
            }
        }
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Library;

use Laminas\Code\Reflection\ClassReflection;
use Laminas\Code\Reflection\FileReflection;
use Laminas\Code\Reflection\MethodReflection;
use Laminas\Code\Reflection\ParameterReflection;
use Magento\Framework\DataObject;
use Magento\TestFramework\Integrity\Library\Injectable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Test for Magento\TestFramework\Integrity\Library\Injectable
 */
class InjectableTest extends TestCase
{
    /**
     * @var Injectable
     */
    protected $injectable;

    /**
     * @var FileReflection
     */
    protected $fileReflection;

    /**
     * @var MockObject
     */
    protected $parameterReflection;

    /**
     * @var MockObject
     */
    protected $declaredClass;

    /**
     * Covered getDependencies
     *
     * @test
     */
    public function testGetDependencies()
    {
        $classReflection = $this->getMockBuilder(
            ClassReflection::class
        )->disableOriginalConstructor()->getMock();

        $classReflection->expects(
            $this->once()
        )->method(
            'getName'
        )->willReturn(
            DataObject::class
        );

        $this->parameterReflection->expects(
            $this->once()
        )->method(
            'getClass'
        )->willReturn(
            $classReflection
        );

        $this->assertEquals(
            [DataObject::class],
            $this->injectable->getDependencies($this->fileReflection)
        );
    }

    /**
     * Covered getDependencies
     *
     * @test
     */
    public function testGetDependenciesWithException()
    {
        $this->parameterReflection->expects($this->once())->method('getClass')->willReturnCallback(

            function () {
                throw new ReflectionException('Class Magento\Framework\DataObject does not exist');
            }

        );

        $this->assertEquals(

            [DataObject::class],
            $this->injectable->getDependencies($this->fileReflection)
        );
    }

    /**
     * Covered with some different exception method
     *
     * @test
     */
    public function testGetDependenciesWithOtherException()
    {
        $this->expectException(ReflectionException::class);

        $this->parameterReflection->expects($this->once())->method('getClass')->willReturnCallback(

            function () {
                throw new ReflectionException('Some message');
            }

        );

        $this->injectable->getDependencies($this->fileReflection);
    }

    /**
     * Covered when method declared in parent class
     *
     * @test
     */
    public function testGetDependenciesWhenMethodDeclaredInParentClass()
    {
        $this->declaredClass->expects($this->once())->method('getName')->willReturn('ParentClass');

        $this->injectable->getDependencies($this->fileReflection);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->injectable = new Injectable();
        $this->fileReflection = $this->getMockBuilder(
            FileReflection::class
        )->disableOriginalConstructor()->getMock();

        $classReflection = $this->getMockBuilder(
            ClassReflection::class
        )->disableOriginalConstructor()->getMock();

        $methodReflection = $this->getMockBuilder(
            MethodReflection::class
        )->disableOriginalConstructor()->getMock();

        $this->parameterReflection = $this->getMockBuilder(
            ParameterReflection::class
        )->disableOriginalConstructor()->getMock();

        $this->declaredClass = $this->getMockBuilder(
            ClassReflection::class
        )->disableOriginalConstructor()->getMock();

        $methodReflection->expects(
            $this->once()
        )->method(
            'getDeclaringClass'
        )->willReturn(
            $this->declaredClass
        );

        $methodReflection->expects(
            $this->any()
        )->method(
            'getParameters'
        )->willReturn(
            [$this->parameterReflection]
        );

        $classReflection->expects(
            $this->once()
        )->method(
            'getMethods'
        )->willReturn(
            [$methodReflection]
        );

        $this->fileReflection->expects(
            $this->once()
        )->method(
            'getClasses'
        )->willReturn(
            [$classReflection]
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Annotation;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Annotation\AppArea;
use Magento\TestFramework\Application;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AppAreaTest extends TestCase
{
    /**
     * @var AppArea
     */
    protected $_object;

    /**
     * @var Application|MockObject
     */
    protected $_applicationMock;

    /**
     * @var TestCase|MockObject
     */
    protected $_testCaseMock;

    /**
     * @param array $annotations
     * @param string $expectedArea
     * @dataProvider getTestAppAreaDataProvider
     */
    public function testGetTestAppArea($annotations, $expectedArea)
    {
        $this->_testCaseMock->expects($this->once())->method('getAnnotations')->willReturn($annotations);
        $this->_applicationMock->expects($this->any())->method('getArea')->willReturn(null);
        $this->_applicationMock->expects($this->once())->method('reinitialize');
        $this->_applicationMock->expects($this->once())->method('loadArea')->with($expectedArea);
        $this->_object->startTest($this->_testCaseMock);
    }

    public function getTestAppAreaDataProvider()
    {
        return [
            'method scope' => [['method' => ['magentoAppArea' => ['Adminhtml']]], 'Adminhtml'],
            'class scope' => [['class' => ['magentoAppArea' => ['frontend']]], 'frontend'],
            'mixed scope' => [
                [
                    'class' => ['magentoAppArea' => ['Adminhtml']],
                    'method' => ['magentoAppArea' => ['frontend']],
                ],
                'frontend',
            ],
            'default area' => [[], 'global']
        ];
    }

    /**
     */
    public function testGetTestAppAreaWithInvalidArea()
    {
        $this->expectException(LocalizedException::class);

        $annotations = ['method' => ['magentoAppArea' => ['some_invalid_area']]];
        $this->_testCaseMock->expects($this->once())->method('getAnnotations')->willReturn($annotations);
        $this->_object->startTest($this->_testCaseMock);
    }

    /**
     * Check startTest() with different allowed area codes.
     *
     * @dataProvider startTestWithDifferentAreaCodes
     * @param string $areaCode
     */
    public function testStartTestWithDifferentAreaCodes(string $areaCode)
    {
        $annotations = ['method' => ['magentoAppArea' => [$areaCode]]];
        $this->_testCaseMock->expects($this->once())->method('getAnnotations')->willReturn($annotations);
        $this->_applicationMock->expects($this->any())->method('getArea')->willReturn(null);
        $this->_applicationMock->expects($this->once())->method('reinitialize');
        $this->_applicationMock->expects($this->once())->method('loadArea')->with($areaCode);
        $this->_object->startTest($this->_testCaseMock);
    }

    public function testStartTestPreventDoubleAreaLoadingAfterReinitialization()
    {
        $annotations = ['method' => ['magentoAppArea' => ['global']]];
        $this->_testCaseMock->expects($this->once())->method('getAnnotations')->willReturn($annotations);
        $this->_applicationMock->expects($this->at(0))->method('getArea')->willReturn('Adminhtml');
        $this->_applicationMock->expects($this->once())->method('reinitialize');
        $this->_applicationMock->expects($this->at(2))->method('getArea')->willReturn('global');
        $this->_applicationMock->expects($this->never())->method('loadArea');
        $this->_object->startTest($this->_testCaseMock);
    }

    public function testStartTestPreventDoubleAreaLoading()
    {
        $annotations = ['method' => ['magentoAppArea' => ['Adminhtml']]];
        $this->_testCaseMock->expects($this->once())->method('getAnnotations')->willReturn($annotations);
        $this->_applicationMock->expects($this->once())->method('getArea')->willReturn('Adminhtml');
        $this->_applicationMock->expects($this->never())->method('reinitialize');
        $this->_applicationMock->expects($this->never())->method('loadArea');
        $this->_object->startTest($this->_testCaseMock);
    }

    /**
     *  Provide test data for testStartTestWithDifferentAreaCodes().
     *
     * @return array
     */
    public function startTestWithDifferentAreaCodes()
    {
        return [
            [
                'area_code' => Area::AREA_GLOBAL,
            ],
            [
                'area_code' => Area::AREA_ADMINHTML,
            ],
            [
                'area_code' => Area::AREA_FRONTEND,
            ],
            [
                'area_code' => Area::AREA_WEBAPI_REST,
            ],
            [
                'area_code' => Area::AREA_WEBAPI_SOAP,
            ],
            [
                'area_code' => Area::AREA_CRONTAB,
            ],
            [
                'area_code' => Area::AREA_GRAPHQL,
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->_testCaseMock = $this->createMock(TestCase::class);
        $this->_applicationMock = $this->createMock(Application::class);
        $this->_object = new AppArea($this->_applicationMock);
    }
}

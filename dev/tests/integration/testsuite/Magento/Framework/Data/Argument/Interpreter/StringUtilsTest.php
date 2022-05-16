<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Data\Argument\Interpreter;

use InvalidArgumentException;
use Magento\Framework\Phrase;
use Magento\Framework\Phrase\RendererInterface;
use Magento\Framework\Stdlib\BooleanUtils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Magento\Framework\Data\Argument\Interpreter\StringUtils
 */
class StringUtilsTest extends TestCase
{
    /**
     * @var BooleanUtils|MockObject
     */
    protected $booleanUtils;
    /**
     * @var StringUtils
     */
    private $model;

    /**
     * Check StringUtils::evaluate can translate incoming $input['value'].
     *
     * @param array $input
     * @param string $expected
     *
     * @dataProvider evaluateDataProvider
     */
    public function testEvaluate($input, $expected)
    {
        $actual = $this->model->evaluate($input);
        $this->assertSame($expected, (string)$actual);
    }

    /**
     * Provide test data and expected results for testEvaluate().
     *
     * @return array
     */
    public function evaluateDataProvider()
    {
        return [
            'no value' => [[], ''],
            'with value' => [['value' => 'some value'], 'some value'],
            'translation required' => [
                ['value' => 'some value', 'translate' => 'true'],
                'some value (translated)',
            ],
            'translation not required' => [['value' => 'some value', 'translate' => 'false'], 'some value'],
        ];
    }

    /**
     * Check StringUtils::evaluate() throws exception in case $input['value'] is not a string.
     *
     * @param array $input
     *
     * @dataProvider evaluateExceptionDataProvider
     */
    public function testEvaluateException($input)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('String value is expected');

        $this->model->evaluate($input);
    }

    /**
     * Provide test data for testEvaluateException.
     *
     * @return array
     */
    public function evaluateExceptionDataProvider()
    {
        return ['not a string' => [['value' => 123]]];
    }

    /**
     * Prepare subject for test.
     */
    protected function setUp(): void
    {
        $this->booleanUtils = $this->createMock(BooleanUtils::class);
        $this->booleanUtils->expects(
            $this->any()
        )->method(
            'toBoolean'
        )->willReturnMap(
            [['true', true], ['false', false]]
        );

        $baseStringUtils = new BaseStringUtils($this->booleanUtils);
        $this->model = new StringUtils($this->booleanUtils, $baseStringUtils);
        /** @var RendererInterface|MockObject $translateRenderer */
        $translateRenderer = $this->getMockBuilder(RendererInterface::class)
            ->setMethods(['render'])
            ->getMockForAbstractClass();
        $translateRenderer->expects($this->any())->method('render')->willReturnCallback(

            function ($input) {
                return end($input) . ' (translated)';
            }

        );
        Phrase::setRenderer($translateRenderer);
    }
}

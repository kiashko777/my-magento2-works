<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Library\PhpParser;

use Magento\TestFramework\Integrity\Library\PhpParser\Throws;
use Magento\TestFramework\Integrity\Library\PhpParser\Tokens;
use Magento\TestFramework\Integrity\Library\PhpParser\Uses;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 */
class ThrowsTest extends TestCase
{
    /**
     * @var Throws
     */
    protected $throws;

    /**
     * @var Tokens|MockObject
     */
    protected $tokens;

    /**
     * Test get throws dependencies
     *
     * @test
     */
    public function testGetDependencies()
    {
        $tokens = [
            0 => [T_THROW, 'throw'],
            1 => [T_WHITESPACE, ' '],
            2 => [T_NEW, 'new'],
            3 => [T_WHITESPACE, ' '],
            4 => [T_NS_SEPARATOR, '\\'],
            5 => [T_STRING, 'Exception'],
            6 => '(',
        ];

        $this->tokens->expects($this->any())->method('getTokenCodeByKey')->willReturnCallback(

            function ($k) use ($tokens) {
                return $tokens[$k][0];
            }

        );

        $this->tokens->expects($this->any())->method('getTokenValueByKey')->willReturnCallback(

            function ($k) use ($tokens) {
                return $tokens[$k][1];
            }

        );

        $throws = new Throws($this->tokens);
        foreach ($tokens as $k => $token) {
            $throws->parse($token, $k);
        }

        $uses = $this->getMockBuilder(
            Uses::class
        )->disableOriginalConstructor()->getMock();

        $uses->expects($this->once())->method('hasUses')->willReturn(true);

        $uses->expects($this->once())->method('getClassNameWithNamespace')->willReturn('\Exception');

        $this->assertEquals(['\Exception'], $throws->getDependencies($uses));
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->tokens = $this->getMockBuilder(
            Tokens::class
        )->disableOriginalConstructor()->getMock();
    }
}

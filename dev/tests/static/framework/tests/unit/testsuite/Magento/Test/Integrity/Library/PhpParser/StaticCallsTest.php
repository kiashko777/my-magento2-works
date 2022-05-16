<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Library\PhpParser;

use Magento\TestFramework\Integrity\Library\PhpParser\StaticCalls;
use Magento\TestFramework\Integrity\Library\PhpParser\Tokens;
use Magento\TestFramework\Integrity\Library\PhpParser\Uses;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 */
class StaticCallsTest extends TestCase
{
    /**
     * @var StaticCalls
     */
    protected $staticCalls;

    /**
     * @var Tokens|MockObject
     */
    protected $tokens;

    /**
     * Test get static call dependencies
     *
     * @test
     */
    public function testGetDependencies()
    {
        $tokens = [
            0 => [T_WHITESPACE, ' '],
            1 => [T_NS_SEPARATOR, '\\'],
            2 => [T_STRING, 'Object'],
            3 => [T_PAAMAYIM_NEKUDOTAYIM, '::'],
        ];

        $this->tokens->expects($this->any())->method('getPreviousToken')->willReturnCallback(

            function ($k) use ($tokens) {
                return $tokens[$k - 1];
            }

        );

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

        $throws = new StaticCalls($this->tokens);
        foreach ($tokens as $k => $token) {
            $throws->parse($token, $k);
        }

        $uses = $this->getMockBuilder(
            Uses::class
        )->disableOriginalConstructor()->getMock();

        $uses->expects($this->once())->method('hasUses')->willReturn(true);

        $uses->expects($this->once())->method('getClassNameWithNamespace')->willReturn('\Object');

        $this->assertEquals(['\Object'], $throws->getDependencies($uses));
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

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Library\PhpParser;

use Magento\TestFramework\Integrity\Library\PhpParser\ParserFactory;
use Magento\TestFramework\Integrity\Library\PhpParser\StaticCalls;
use Magento\TestFramework\Integrity\Library\PhpParser\Throws;
use Magento\TestFramework\Integrity\Library\PhpParser\Tokens;
use Magento\TestFramework\Integrity\Library\PhpParser\Uses;
use PHPUnit\Framework\TestCase;

/**
 */
class ParserFactoryTest extends TestCase
{
    /**
     * @var Tokens
     */
    protected $tokens;

    /**
     * Covered createParsers method
     *
     * @test
     */
    public function testCreateParsers()
    {
        $parseFactory = new ParserFactory();
        $parseFactory->createParsers($this->tokens);
        $this->assertInstanceOf(
            Uses::class,
            $parseFactory->getUses()
        );
        $this->assertInstanceOf(
            StaticCalls::class,
            $parseFactory->getStaticCalls()
        );
        $this->assertInstanceOf(
            Throws::class,
            $parseFactory->getThrows()
        );
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

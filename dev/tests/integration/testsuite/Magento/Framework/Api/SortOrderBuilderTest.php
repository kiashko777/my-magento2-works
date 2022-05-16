<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Api;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test of building the Data Object
 */
class SortOrderBuilderTest extends TestCase
{
    /**
     * @var SortOrderBuilder
     */
    private $interceptedBuilder;

    /**
     * Test Builder successfully creates object when Interceptor instance is provided.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $this->assertEquals(SortOrder::class, get_class($this->interceptedBuilder->create()));
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->interceptedBuilder = Bootstrap::getObjectManager()->get(SortOrderBuilder::class . '\Interceptor');
    }
}

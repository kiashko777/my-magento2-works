<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SwaggerWebapi\Block\Swagger;

use Magento\Framework\App\State;
use Magento\Framework\View\LayoutInterface;
use Magento\Swagger\Block\Index;
use Magento\SwaggerWebapi\Model\SchemaType\Rest;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    private $block;

    /**
     * Test that the Swagger UI outputs rest as the default when there is no type parameter supplied via URL.
     */
    public function testDefaultSchemaUrlOutput()
    {
        $this->assertStringEndsWith('/rest/all/schema?services=all', $this->block->getSchemaUrl());
    }

    /**
     * Test that Swagger UI outputs the supplied store code when it is specified.
     */
    public function testSchemaUrlOutputWithStore()
    {
        $this->block->getRequest()->setParams([
            'store' => 'custom',
        ]);

        $this->assertStringEndsWith('/rest/custom/schema?services=all', $this->block->getSchemaUrl());
    }

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->get(State::class)
            ->setAreaCode('frontend');

        $this->block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Index::class,
            '',
            [
                'data' => [
                    'schema_types' => [
                        'rest' => Bootstrap::getObjectManager()->get(
                            Rest::class
                        )
                    ],
                    'default_schema_type_code' => 'rest'
                ]
            ]
        );
    }
}

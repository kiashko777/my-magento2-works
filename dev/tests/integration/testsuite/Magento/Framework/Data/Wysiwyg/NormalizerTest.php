<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Data\Wysiwyg;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
    /**
     * @var Normalizer
     */
    private $normalizer;

    public function testReplaceReservedCharacters()
    {
        $content = '{}\\""[]';
        $expected = '^[^]|``[]';
        $this->assertEquals($expected, $this->normalizer->replaceReservedCharacters($content));
    }

    public function testRestoreReservedCharacters()
    {
        $content = '^[^]|``[]';
        $expected = '{}\\""[]';
        $this->assertEquals($expected, $this->normalizer->restoreReservedCharacters($content));
    }

    public function testReplaceAndRestoreReservedCharacters()
    {
        $value = '{"1":{"type":"Magento\\CatalogWidget\\Model\\Rule\\Condition\\Combine",'
            . '"aggregator":"all","value":"1","new_child":""},"1--1":{"type":'
            . '"Magento\\CatalogWidget\\Model\\Rule\\Condition\\Products","attribute":"pattern",'
            . '"operator":"{}","value":["212,213"]}}';
        $this->assertEquals(
            $value,
            $this->normalizer->restoreReservedCharacters(
                $this->normalizer->replaceReservedCharacters($value)
            )
        );
    }

    protected function setUp(): void
    {
        $this->normalizer = Bootstrap::getObjectManager()->create(
            Normalizer::class
        );
    }
}

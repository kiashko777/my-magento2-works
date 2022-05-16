<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Search\Request\Config;

use DOMDocument;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /** @var  Converter */
    protected $object;

    public function testConvert()
    {
        $document = new DOMDocument();
        $document->load(__DIR__ . '../../../_files/search_request.xml');
        $result = $this->object->convert($document);
        $expected = include __DIR__ . '/../../_files/search_request_config.php';
        $this->assertEquals($expected, $result);
    }

    protected function setUp(): void
    {
        $this->object = Bootstrap::getObjectManager()
            ->create(Converter::class);
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Search\Request\Config;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FileSystemReaderTest extends TestCase
{
    /** @var  FilesystemReader */
    protected $object;

    public function testRead()
    {
        $result = $this->object->read();
        // Filter values added by \Magento\CatalogSearch\Model\Search\ReaderPlugin
        $result = array_intersect_key($result, array_flip(['bool_query', 'filter_query', 'new_match_query']));
        $expected = include __DIR__ . '/../../_files/search_request_merged.php';
        $this->assertEquals($expected, $result);
    }

    protected function setUp(): void
    {
        $fileResolver = Bootstrap::getObjectManager()->create(
            FileResolverStub::class
        );
        $this->object = Bootstrap::getObjectManager()->create(
            FilesystemReader::class,
            ['fileResolver' => $fileResolver]
        );
    }
}

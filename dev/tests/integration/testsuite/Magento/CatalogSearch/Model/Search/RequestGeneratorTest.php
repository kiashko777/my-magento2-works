<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Model\Search;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\CatalogSearch\Model\Search\RequestGenerator.
 *
 * @magentoAppIsolation enabled
 * @magentoDataFixture Magento/CatalogSearch/_files/search_attributes.php
 */
class RequestGeneratorTest extends TestCase
{
    /**
     * @var RequestGenerator
     */
    protected $model;

    public function testGenerate()
    {
        $requests = $this->model->generate();

        //Quick Search
        $this->assertArrayHasKey('quick_search_container', $requests);
        $quickSearch = $requests['quick_search_container'];
        $message = 'Unexpected attribute';
        $this->assertArrayHasKey('test_quick_search_bucket', $quickSearch['aggregations'], $message);
        $this->assertArrayNotHasKey('test_catalog_view_bucket', $quickSearch['aggregations'], $message);

        //Catalog View
        $this->assertArrayHasKey('catalog_view_container', $requests);
        $catalogView = $requests['catalog_view_container'];
        $this->assertArrayNotHasKey('test_quick_search_bucket', $catalogView['aggregations'], $message);
        $this->assertArrayHasKey('test_catalog_view_bucket', $catalogView['aggregations'], $message);
    }

    protected function setUp(): void
    {
        $this->model = Bootstrap::getObjectManager()
            ->create(RequestGenerator::class);
    }
}

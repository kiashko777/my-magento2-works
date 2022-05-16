<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Ui\DataProvider\Product\Attributes;

use Magento\Framework\App\RequestInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ListingTest extends TestCase
{
    /** @var Listing */
    private $dataProvider;

    /** @var RequestInterface */
    private $request;

    public function testGetDataSortedAsc()
    {
        $this->dataProvider->addOrder('attribute_code', 'asc');
        $data = $this->dataProvider->getData();
        $this->assertEquals(2, $data['totalRecords']);
        $this->assertEquals('color', $data['items'][0]['attribute_code']);
        $this->assertEquals('manufacturer', $data['items'][1]['attribute_code']);
    }

    public function testGetDataSortedDesc()
    {
        $this->dataProvider->addOrder('attribute_code', 'desc');
        $data = $this->dataProvider->getData();
        $this->assertEquals(2, $data['totalRecords']);
        $this->assertEquals('manufacturer', $data['items'][0]['attribute_code']);
        $this->assertEquals('color', $data['items'][1]['attribute_code']);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var RequestInterface $request */
        $this->request = $objectManager->get(RequestInterface::class);

        /** Default Attribute Set Id is equal 4 */
        $this->request->setParams(['template_id' => 4]);

        $this->dataProvider = $objectManager->create(
            Listing::class,
            [
                'name' => 'product_attributes_grid_data_source',
                'primaryFieldName' => 'attribute_id',
                'requestFieldName' => 'id',
                'request' => $this->request
            ]
        );
    }
}

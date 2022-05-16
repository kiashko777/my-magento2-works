<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Attribute\Source;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CountryofmanufactureTest extends TestCase
{
    /**
     * @var Countryofmanufacture
     */
    private $model;

    public function testGetAllOptions()
    {
        $allOptions = $this->model->getAllOptions();
        $cachedAllOptions = $this->model->getAllOptions();
        $this->assertEquals($allOptions, $cachedAllOptions);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(
            Countryofmanufacture::class
        );
    }
}

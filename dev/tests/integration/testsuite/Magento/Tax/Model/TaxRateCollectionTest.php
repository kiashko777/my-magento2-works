<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model;

use Magento\Tax\Model\ResourceModel\Calculation\Rate\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TaxRateCollectionTest extends TestCase
{
    public function testCreateTaxRateCollectionItem()
    {
        /** @var Collection $collection */
        $collection = Bootstrap::getObjectManager()->get(
            Collection::class
        );
        $dbTaxRatesQty = $collection->count();
        if (($dbTaxRatesQty == 0) || ($collection->getFirstItem()->getId() != 1)) {
            $this->fail("Preconditions failed.");
        }
        /** @var TaxRateCollection $taxRatesCollection */
        $taxRatesCollection = Bootstrap::getObjectManager()
            ->create(TaxRateCollection::class);
        $collectionTaxRatesQty = $taxRatesCollection->count();
        $this->assertEquals($dbTaxRatesQty, $collectionTaxRatesQty, 'Tax rates quantity is invalid.');
        $taxRate = $taxRatesCollection->getFirstItem()->getData();
        $expectedTaxRateData = [
            'code' => 'US-CA-*-Rate 1',
            'tax_calculation_rate_id' => '1',
            'rate' => 8.25,
            'region_name' => 'CA',
            'tax_country_id' => 'US',
            'tax_postcode' => '*',
            'tax_region_id' => '12',
            'titles' => [],
            'zip_is_range' => null,
            'zip_from' => null,
            'zip_to' => null,
        ];
        $this->assertEquals($expectedTaxRateData, $taxRate, 'Tax rate data is invalid.');
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\TaxClass\Source;

use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\ResourceModel\TaxClass\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGetAllOptions()
    {
        /** @var Collection $collection */
        $collection = Bootstrap::getObjectManager()->get(Collection::class);
        $expectedResult = [];
        /** @var ClassModel $taxClass */
        foreach ($collection as $taxClass) {
            if ($taxClass->getClassType() == TaxClassManagementInterface::TYPE_PRODUCT) {
                $expectedResult[] = ['value' => $taxClass->getId(), 'label' => $taxClass->getClassName()];
            }
        }
        if (empty($expectedResult)) {
            $this->fail('Preconditions failed: At least one tax class should be available.');
        }
        /** @var Product $source */
        $source = Bootstrap::getObjectManager()->get(Product::class);
        $this->assertEquals(
            $expectedResult,
            $source->getAllOptions(false),
            'Tax Class options are invalid.'
        );
    }

    public function testGetAllOptionsWithDefaultValues()
    {
        /** @var Collection $collection */
        $collection = Bootstrap::getObjectManager()->get(Collection::class);
        $expectedResult = [];
        /** @var ClassModel $taxClass */
        foreach ($collection as $taxClass) {
            if ($taxClass->getClassType() == TaxClassManagementInterface::TYPE_PRODUCT) {
                $expectedResult[] = ['value' => $taxClass->getId(), 'label' => $taxClass->getClassName()];
            }
        }
        if (empty($expectedResult)) {
            $this->fail('Preconditions failed: At least one tax class should be available.');
        }
        $expectedResult = array_merge([['value' => '0', 'label' => __('None')]], $expectedResult);
        /** @var Product $source */
        $source = Bootstrap::getObjectManager()->get(Product::class);
        $this->assertEquals(
            $expectedResult,
            $source->getAllOptions(true),
            'Tax Class options are invalid.'
        );
    }
}

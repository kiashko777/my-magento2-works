<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\ImportExport\Model\Import\Entity\AbstractEav
 */

namespace Magento\ImportExport\Model\Import\Entity;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EavAbstractTest extends TestCase
{
    /**
     * Model object which used for tests
     *
     * @var AbstractEav|MockObject
     */
    protected $_model;

    /**
     * Test for method getAttributeOptions()
     */
    public function testGetAttributeOptions()
    {
        $indexAttributeCode = 'gender';

        /** @var $attributeCollection Collection */
        $attributeCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $attributeCollection->addFieldToFilter(
            'attribute_code',
            ['in' => [$indexAttributeCode, 'group_id']]
        );
        /** @var $attribute Attribute */
        foreach ($attributeCollection as $attribute) {
            $index = $attribute->getAttributeCode() == $indexAttributeCode ? 'value' : 'label';
            $expectedOptions = [];
            foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                if (is_array($option['value'])) {
                    foreach ($option['value'] as $value) {
                        $expectedOptions[strtolower($value[$index])] = $value['value'];
                    }
                } else {
                    $expectedOptions[strtolower($option[$index])] = $option['value'];
                }
            }
            $actualOptions = $this->_model->getAttributeOptions($attribute, [$indexAttributeCode]);
            asort($expectedOptions);
            asort($actualOptions);
            $this->assertEquals($expectedOptions, $actualOptions);
        }
    }

    /**
     * Create all necessary data for tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_model = $this->getMockForAbstractClass(
            AbstractEav::class,
            [],
            '',
            false
        );
    }
}

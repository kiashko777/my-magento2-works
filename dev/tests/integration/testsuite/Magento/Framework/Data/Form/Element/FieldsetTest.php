<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Fieldset
 */

namespace Magento\Framework\Data\Form\Element;

use Magento\Framework\Data\Form\ElementFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FieldsetTest extends TestCase
{
    /**
     * @var Fieldset
     */
    protected $_fieldset;

    /**
     * Test whether fieldset contains advanced section or not
     *
     * @dataProvider fieldsDataProvider
     */
    public function testHasAdvanced(array $fields, $expect)
    {
        $this->_fillFieldset($fields);
        $this->assertEquals($expect, $this->_fieldset->hasAdvanced());
    }

    /**
     * @param array $fields
     */
    protected function _fillFieldset(array $fields)
    {
        foreach ($fields as $field) {
            $this->_fieldset->addField($field[0], $field[1], $field[2], $field[3], $field[4]);
        }
    }

    /**
     * Test getting advanced section label
     */
    public function testAdvancedLabel()
    {
        $this->assertEmpty($this->_fieldset->getAdvancedLabel());
        $label = 'Test Label';
        $this->_fieldset->setAdvancedLabel($label);
        $this->assertEquals($label, $this->_fieldset->getAdvancedLabel());
    }

    /**
     * @dataProvider getChildrenDataProvider
     */
    public function testGetChildren($fields, $expect)
    {
        $this->_fillFieldset($fields);
        $this->assertCount($expect, $this->_fieldset->getChildren());
    }

    /**
     * @dataProvider getBasicChildrenDataProvider
     * @param array $fields
     * @param int $expect
     */
    public function testGetBasicChildren($fields, $expect)
    {
        $this->_fillFieldset($fields);
        $this->assertCount($expect, $this->_fieldset->getBasicChildren());
    }

    /**
     * @dataProvider getBasicChildrenDataProvider
     * @param array $fields
     * @param int $expect
     */
    public function testGetCountBasicChildren($fields, $expect)
    {
        $this->_fillFieldset($fields);
        $this->assertEquals($expect, $this->_fieldset->getCountBasicChildren());
    }

    /**
     * @return array
     */
    public function getBasicChildrenDataProvider()
    {
        $data = $this->getChildrenDataProvider();
        // set isAdvanced flag
        $data[0][0][0][4] = true;
        return $data;
    }

    /**
     * @return array
     */
    public function getChildrenDataProvider()
    {
        $data = $this->fieldsDataProvider();
        $textField = $data[1][0][0];
        $fieldsetField = $textField;
        $fieldsetField[1] = 'fieldset';
        $result = [[[$fieldsetField], 0], [[$textField], 1]];
        return $result;
    }

    /**
     * @return array
     */
    public function fieldsDataProvider()
    {
        return [
            [
                [
                    [
                        'code',
                        'text',
                        ['name' => 'code', 'label' => 'Name', 'class' => 'required-entry', 'required' => true],
                        false,
                        false,
                    ],
                    [
                        'tax_rate',
                        'multiselect',
                        [
                            'name' => 'tax_rate',
                            'label' => 'Tax Rate',
                            'class' => 'required-entry',
                            'values' => ['A', 'B', 'C'],
                            'value' => 1,
                            'required' => true
                        ],
                        false,
                        false
                    ],
                    [
                        'priority',
                        'text',
                        [
                            'name' => 'priority',
                            'label' => 'Priority',
                            'class' => 'validate-not-negative-number',
                            'value' => 1,
                            'required' => true,
                            'note' => 'Tax rates at the same priority are added, others are compounded.'
                        ],
                        false,
                        true
                    ],
                    [
                        'priority',
                        'text',
                        [
                            'name' => 'priority',
                            'label' => 'Priority',
                            'class' => 'validate-not-negative-number',
                            'value' => 1,
                            'required' => true,
                            'note' => 'Tax rates at the same priority are added, others are compounded.'
                        ],
                        false,
                        true
                    ],
                ],
                true,
            ],
            [
                [
                    [
                        'code',
                        'text',
                        ['name' => 'code', 'label' => 'Name', 'class' => 'required-entry', 'required' => true],
                        false,
                        false,
                    ],
                    [
                        'tax_rate',
                        'multiselect',
                        [
                            'name' => 'tax_rate',
                            'label' => 'Tax Rate',
                            'class' => 'required-entry',
                            'values' => ['A', 'B', 'C'],
                            'value' => 1,
                            'required' => true
                        ],
                        false,
                        false
                    ],
                ],
                false
            ]
        ];
    }

    /**
     * @dataProvider getAdvancedChildrenDataProvider
     * @param array $fields
     * @param int $expect
     */
    public function testGetAdvancedChildren($fields, $expect)
    {
        $this->_fillFieldset($fields);
        $this->assertCount($expect, $this->_fieldset->getAdvancedChildren());
    }

    /**
     * @return array
     */
    public function getAdvancedChildrenDataProvider()
    {
        $data = $this->getChildrenDataProvider();
        // change isAdvanced flag
        $data[0][0][0][4] = true;
        // change expected results
        $data[0][1] = 1;
        $data[1][1] = 0;
        return $data;
    }

    /**
     * @dataProvider getSubFieldsetDataProvider
     * @param array $fields
     * @param int $expect
     */
    public function testGetSubFieldset($fields, $expect)
    {
        $this->_fillFieldset($fields);
        $this->assertCount($expect, $this->_fieldset->getAdvancedChildren());
    }

    /**
     * @return array
     */
    public function getSubFieldsetDataProvider()
    {
        $data = $this->fieldsDataProvider();
        $textField = $data[1][0][0];
        $fieldsetField = $textField;
        $fieldsetField[1] = 'fieldset';
        $advancedFieldsetFld = $fieldsetField;
        // set isAdvanced flag
        $advancedFieldsetFld[4] = true;
        $result = [[[$fieldsetField, $textField, $advancedFieldsetFld], 1]];
        return $result;
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $elementFactory ElementFactory */
        $elementFactory = $objectManager->create(ElementFactory::class);
        $this->_fieldset = $elementFactory->create(Fieldset::class, []);
    }
}

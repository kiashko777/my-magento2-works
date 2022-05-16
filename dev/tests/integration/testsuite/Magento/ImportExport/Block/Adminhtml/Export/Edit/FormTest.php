<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Block\Adminhtml\Export\Edit;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for block \Magento\ImportExport\Block\Adminhtml\Export\Edit\Form
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * Testing model
     *
     * @var Form
     */
    protected $_model;

    /**
     * Expected form fieldsets and fields
     * array (
     *     <fieldset_id> => array(
     *         <element_id> => <element_name>,
     *         ...
     *     ),
     *     ...
     * )
     *
     * @var array
     */
    protected $_expectedFields = ['base_fieldset' => [
        'entity' => 'entity',
        'file_format' => 'file_format',
        'fields_enclosure' => 'fields_enclosure'
    ]];

    /**
     * Test preparing of form
     *
     * @covers \Magento\ImportExport\Block\Adminhtml\Export\Edit\Form::_prepareForm
     */
    public function testPrepareForm()
    {
        // invoking _prepareForm
        $this->_model->toHtml();

        // get fieldset list
        $actualFieldsets = [];
        $formElements = $this->_model->getForm()->getElements();
        foreach ($formElements as $formElement) {
            if ($formElement instanceof Fieldset) {
                $actualFieldsets[] = $formElement;
            }
        }

        // assert fieldsets and fields
        $this->assertSameSize($this->_expectedFields, $actualFieldsets);
        /** @var $actualFieldset Fieldset */
        foreach ($actualFieldsets as $actualFieldset) {
            $this->assertArrayHasKey($actualFieldset->getId(), $this->_expectedFields);
            $expectedFields = $this->_expectedFields[$actualFieldset->getId()];
            /** @var $actualField AbstractElement */
            foreach ($actualFieldset->getElements() as $actualField) {
                $this->assertArrayHasKey($actualField->getId(), $expectedFields);
                $this->assertEquals($expectedFields[$actualField->getId()], $actualField->getName());
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_model = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Form::class
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Block\Adminhtml\Import\Edit;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\View\LayoutInterface;
use Magento\ImportExport\Model\Import;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Tests for block \Magento\ImportExport\Block\Adminhtml\Import\Edit\FormTest
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * List of expected fieldsets in import edit form
     *
     * @var array
     */
    protected $_expectedFieldsets = ['base_fieldset', 'upload_file_fieldset'];

    /**
     * Test content of form after _prepareForm
     */
    public function testPrepareForm()
    {
        $formBlock = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Form::class
        );
        $prepareForm = new ReflectionMethod(
            Form::class,
            '_prepareForm'
        );
        $prepareForm->setAccessible(true);
        $prepareForm->invoke($formBlock);

        // check form
        $form = $formBlock->getForm();
        $this->assertInstanceOf(\Magento\Framework\Data\Form::class, $form, 'Incorrect import form class.');
        $this->assertTrue($form->getUseContainer(), 'Form should use container.');

        // check form fieldsets
        $formFieldsets = [];
        $formElements = $form->getElements();
        foreach ($formElements as $element) {
            /** @var $element AbstractElement */
            if (in_array($element->getId(), $this->_expectedFieldsets)) {
                $formFieldsets[] = $element;
            }
        }
        $this->assertSameSize($this->_expectedFieldsets, $formFieldsets);
        foreach ($formFieldsets as $fieldset) {
            $this->assertInstanceOf(
                Fieldset::class,
                $fieldset,
                'Incorrect fieldset class.'
            );
        }
    }

    /**
     * Add behaviour fieldsets to expected fieldsets
     *
     * @static
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $importModel = $objectManager->create(Import::class);
        $uniqueBehaviors = $importModel->getUniqueEntityBehaviors();
        foreach (array_keys($uniqueBehaviors) as $behavior) {
            $this->_expectedFieldsets[] = $behavior . '_fieldset';
        }
    }
}

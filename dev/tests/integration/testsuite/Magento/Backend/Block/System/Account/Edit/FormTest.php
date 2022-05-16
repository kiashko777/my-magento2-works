<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\System\Account\Edit;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    public function testPrepareForm()
    {
        $user = Bootstrap::getObjectManager()->create(
            User::class
        )->loadByUsername(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME
        );

        /** @var $session Session */
        $session = Bootstrap::getObjectManager()->get(
            Session::class
        );
        $session->setUser($user);

        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );

        /** @var Form */
        $block = $layout->createBlock(Form::class);
        $block->toHtml();

        $form = $block->getForm();

        $this->assertInstanceOf(\Magento\Framework\Data\Form::class, $form);
        $this->assertEquals('post', $form->getData('method'));
        $this->assertEquals($block->getUrl('Adminhtml/system_account/save'), $form->getData('action'));
        $this->assertEquals('edit_form', $form->getId());
        $this->assertTrue($form->getUseContainer());

        $expectedFieldset = [
            'username' => [
                'name' => 'username',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('username'),
            ],
            'firstname' => [
                'name' => 'firstname',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('firstname'),
            ],
            'lastname' => [
                'name' => 'lastname',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('lastname'),
            ],
            'email' => [
                'name' => 'email',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('email'),
            ],
            'password' => ['name' => 'password', 'type' => 'password', 'required' => false],
            'confirmation' => ['name' => 'password_confirmation', 'type' => 'password', 'required' => false],
            'interface_locale' => ['name' => 'interface_locale', 'type' => 'select', 'required' => false],
        ];

        foreach ($expectedFieldset as $fieldId => $field) {
            $element = $form->getElement($fieldId);
            $this->assertInstanceOf(AbstractElement::class, $element);
            $this->assertEquals($field['name'], $element->getName(), 'Wrong \'' . $fieldId . '\' field name');
            $this->assertEquals($field['type'], $element->getType(), 'Wrong \'' . $fieldId . ' field type');
            $this->assertEquals(
                $field['required'],
                $element->getData('required'),
                'Wrong \'' . $fieldId . '\' requirement state'
            );
            if (array_key_exists('value', $field)) {
                $this->assertEquals($field['value'], $element->getData('value'), 'Wrong \'' . $fieldId . '\' value');
            }
        }
    }
}

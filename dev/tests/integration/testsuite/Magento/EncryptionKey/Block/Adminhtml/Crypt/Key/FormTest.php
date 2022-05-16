<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\EncryptionKey\Block\Adminhtml\Crypt\Key;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Test class for \Magento\EncryptionKey\Block\Adminhtml\Crypt\Key\Form
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        $objectManager->get(DesignInterface::class)
            ->setArea(FrontNameResolver::AREA_CODE)
            ->setDefaultDesignTheme();

        $block = $objectManager->get(LayoutInterface::class)
            ->createBlock(Form::class);

        $prepareFormMethod = new ReflectionMethod(
            Form::class,
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();

        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());

        foreach (['enc_key_note', 'generate_random', 'crypt_key', 'main_fieldset'] as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
        }

        $generateRandomField = $form->getElement('generate_random');
        $this->assertEquals('select', $generateRandomField->getType());
        $this->assertEquals([0 => 'No', 1 => 'Yes'], $generateRandomField->getOptions());

        $cryptKeyField = $form->getElement('crypt_key');
        $this->assertEquals('text', $cryptKeyField->getType());
        $this->assertEquals('crypt_key', $cryptKeyField->getName());
    }
}

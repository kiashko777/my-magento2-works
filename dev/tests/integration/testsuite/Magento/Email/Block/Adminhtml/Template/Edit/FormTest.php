<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Email\Block\Adminhtml\Template\Edit;

use Magento\Email\Model\Template;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Test class for \Magento\Email\Block\Adminhtml\Template\Edit\Form
 * @magentoAppArea Adminhtml
 * @magentoAppIsolation enabled
 */
class FormTest extends TestCase
{
    /** @var string[] */
    protected $expectedFields;

    /** @var Template */
    protected $template;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var Form */
    protected $block;

    /** @var ReflectionMethod */
    protected $prepareFormMethod;

    /**
     * @covers \Magento\Email\Block\Adminhtml\Template\Edit\Form::_prepareForm
     */
    public function testPrepareFormWithTemplateId()
    {
        $this->expectedFields[] = 'currently_used_for';
        $this->runTest();
    }

    protected function runTest()
    {
        $this->prepareFormMethod->invoke($this->block);
        $form = $this->block->getForm();
        foreach ($this->expectedFields as $key) {
            $this->assertNotNull($form->getElement($key));
        }
        $this->assertGreaterThan(0, strpos($form->getElement('insert_variable')->getData('text'), 'Insert Variable'));
    }

    protected function setUp(): void
    {
        $this->expectedFields = [
            'base_fieldset',
            'template_code',
            'template_subject',
            'orig_template_variables',
            'variables',
            'template_variables',
            'insert_variable',
            'template_text',
            'template_styles'
        ];

        $this->objectManager = Bootstrap::getObjectManager();
        $this->template = $this->objectManager->get(Template::class)
            ->setId(1)
            ->setTemplateType(TemplateTypesInterface::TYPE_HTML);

        $this->block = $this->objectManager->create(
            Form::class,
            [
                'data' => [
                    'email_template' => $this->template
                ]
            ]
        );
        $this->prepareFormMethod = new ReflectionMethod(
            Form::class,
            '_prepareForm'
        );
        $this->prepareFormMethod->setAccessible(true);
    }
}

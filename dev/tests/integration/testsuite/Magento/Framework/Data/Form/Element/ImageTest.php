<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Image
 */

namespace Magento\Framework\Data\Form\Element;

use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\ElementFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    /**
     * @var Image
     */
    protected $imageElement;

    public function testGetElementHtml()
    {
        $filePath = 'some/path/to/file.jpg';
        $this->imageElement->setValue($filePath);
        $html = $this->imageElement->getElementHtml();

        $this->assertStringContainsString('media/' . $filePath, $html);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $elementFactory ElementFactory */
        $elementFactory = $objectManager->create(ElementFactory::class);
        $this->imageElement = $elementFactory->create(Image::class, []);
        $form = $objectManager->create(Form::class);
        $this->imageElement->setForm($form);
    }
}

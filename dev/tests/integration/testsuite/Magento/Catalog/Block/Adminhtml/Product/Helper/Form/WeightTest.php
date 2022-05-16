<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Catalog\Model\Product\Type\Virtual;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class WeightTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var FormFactory
     */
    protected $_formFactory;

    /**
     * @return array
     */
    public static function virtualTypesDataProvider()
    {
        return [
            [Virtual::class],
            [\Magento\Downloadable\Model\Product\Type::class]
        ];
    }

    /**
     * @return array
     */
    public static function physicalTypesDataProvider()
    {
        return [[Simple::class], [Type::class]];
    }

    /**
     * @param string $type
     * @dataProvider virtualTypesDataProvider
     */
    public function testProductWithoutWeight($type)
    {
        /** @var $currentProduct Product */
        $currentProduct = $this->_objectManager->create(Product::class);
        $currentProduct->setTypeInstance($this->_objectManager->create($type));
        /** @var $block Weight */
        $block = $this->_objectManager->create(Weight::class);
        $form = $this->_formFactory->create();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertMatchesRegularExpression(
            '/value="0".*checked="checked"/',
            $block->getElementHtml(),
            '"Does this have a weight" is set to "Yes" for virtual products'
        );
    }

    /**
     * @param string $type
     * @dataProvider physicalTypesDataProvider
     */
    public function testProductHasWeight($type)
    {
        /** @var $currentProduct Product */
        $currentProduct = $this->_objectManager->create(Product::class);
        $currentProduct->setTypeInstance($this->_objectManager->create($type));

        /** @var $block Weight */
        $block = $this->_objectManager->create(Weight::class);
        $form = $this->_formFactory->create();
        $form->setDataObject($currentProduct);
        $block->setForm($form);
        $this->assertDoesNotMatchRegularExpression(
            '/value="0".*checked="checked"/',
            $block->getElementHtml(),
            '"Does this have a weight" is set to "No" for physical products'
        );
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_formFactory = $this->_objectManager->create(FormFactory::class);
    }
}

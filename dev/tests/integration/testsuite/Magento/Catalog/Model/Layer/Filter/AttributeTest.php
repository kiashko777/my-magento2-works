<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Attribute.
 *
 * @magentoDbIsolation disabled
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/_files/attribute_with_option.php
 */
class AttributeTest extends TestCase
{
    /**
     * @var Attribute
     */
    protected $_model;

    /**
     * @var int
     */
    protected $_attributeOptionId;

    /**
     * @var Layer
     */
    protected $_layer;

    public function testOptionIdNotEmpty()
    {
        $this->assertNotEmpty($this->_attributeOptionId, 'Fixture attribute option id.'); // just in case
    }

    public function testApplyInvalid()
    {
        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());
        $objectManager = Bootstrap::getObjectManager();
        $request = $objectManager->get(Request::class);
        $request->setParam('attribute', []);
        $this->_model->apply(
            $request,
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Text::class
            )
        );

        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());
    }

    public function testApply()
    {
        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());

        $objectManager = Bootstrap::getObjectManager();
        $request = $objectManager->get(Request::class);
        $request->setParam('attribute', $this->_attributeOptionId);
        $this->_model->apply($request);

        $this->assertNotEmpty($this->_model->getLayer()->getState()->getFilters());
    }

    public function testGetItems()
    {
        $items = $this->_model->getItems();

        $this->assertIsArray($items);
        $this->assertCount(1, $items);

        /** @var $item Item */
        $item = $items[0];

        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame($this->_model, $item->getFilter());
        $this->assertEquals('Option Label', $item->getLabel());
        $this->assertEquals($this->_attributeOptionId, $item->getValue());
        $this->assertEquals(1, $item->getCount());
    }

    protected function setUp(): void
    {
        /** @var $attribute \Magento\Catalog\Model\Entity\Attribute */
        $attribute = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Entity\Attribute::class
        );
        $attribute->loadByCode('catalog_product', 'attribute_with_option');
        foreach ($attribute->getSource()->getAllOptions() as $optionInfo) {
            if ($optionInfo['label'] == 'Option Label') {
                $this->_attributeOptionId = $optionInfo['value'];
                break;
            }
        }

        $this->_layer = Bootstrap::getObjectManager()
            ->create(\Magento\Catalog\Model\Layer\Category::class);
        $this->_model = Bootstrap::getObjectManager()
            ->create(Attribute::class, ['layer' => $this->_layer]);
        $this->_model->setData([
            'attribute_model' => $attribute,
        ]);
    }
}

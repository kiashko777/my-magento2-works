<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Decimal.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/_files/attribute_weight_filterable.php
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class DecimalTest extends TestCase
{
    /**
     * @var Decimal
     */
    protected $_model;

    public function testApplyNothing()
    {
        $this->assertEmpty($this->_model->getData('range'));
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $this->_model->apply($request);

        $this->assertEmpty($this->_model->getData('range'));
    }

    public function testApplyInvalid()
    {
        $this->assertEmpty($this->_model->getData('range'));
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $request->setParam('decimal', 'non-decimal');
        $this->_model->apply($request);

        $this->assertEmpty($this->_model->getData('range'));
    }

    public function testApply()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $request->setParam('decimal', '1,100');
        $this->_model->apply($request);
    }

    protected function setUp(): void
    {
        $category = Bootstrap::getObjectManager()
            ->create(
                \Magento\Catalog\Model\Category::class
            );
        $category->load(4);

        $layer = Bootstrap::getObjectManager()
            ->create(
                \Magento\Catalog\Model\Layer\Category::class,
                [
                    'data' => ['current_category' => $category]
                ]
            );

        /** @var $attribute \Magento\Catalog\Model\Entity\Attribute */
        $attribute = Bootstrap::getObjectManager()
            ->create(
                \Magento\Catalog\Model\Entity\Attribute::class
            );
        $attribute->loadByCode('catalog_product', 'weight');

        $this->_model = Bootstrap::getObjectManager()
            ->create(Decimal::class, ['layer' => $layer]);
        $this->_model->setAttributeModel($attribute);
    }
}

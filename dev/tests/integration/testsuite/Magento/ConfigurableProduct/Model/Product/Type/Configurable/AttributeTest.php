<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Model\Product\Type\Configurable;

use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    /**
     * @var Attribute
     */
    protected $_model;

    public function testGetLabel()
    {
        $this->assertEmpty($this->_model->getLabel());
        $this->_model->setProductAttribute(new DataObject(['store_label' => 'Store Label']));
        $this->assertEquals('Store Label', $this->_model->getLabel());

        $this->_model->setUseDefault(
            1
        )->setProductAttribute(
            new DataObject(['store_label' => 'Other Label'])
        );
        $this->assertEquals('Other Label', $this->_model->getLabel());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Attribute::class
        );
    }
}

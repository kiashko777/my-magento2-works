<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Helper\Product;

use Magento\Catalog\Helper\Product\Flat\Indexer;
use Magento\Catalog\Model\Indexer\Product\Flat\State;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FlatTest extends TestCase
{
    /**
     * @var Indexer
     */
    protected $_helper;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->_state->isFlatEnabled());
    }

    public function testIsAddFilterableAttributesDefault()
    {
        $this->assertEquals(0, $this->_helper->isAddFilterableAttributes());
    }

    public function testIsAddFilterableAttributes()
    {
        $helper = Bootstrap::getObjectManager()->create(
            Indexer::class,
            ['addFilterableAttrs' => 1]
        );
        $this->assertEquals(1, $helper->isAddFilterableAttributes());
    }

    public function testIsAddChildDataDefault()
    {
        $this->assertEquals(0, $this->_helper->isAddChildData());
    }

    public function testIsAddChildData()
    {
        $helper = Bootstrap::getObjectManager()->create(
            Indexer::class,
            ['addChildData' => 1]
        );
        $this->assertEquals(1, $helper->isAddChildData());
    }

    protected function setUp(): void
    {
        $this->_helper = Bootstrap::getObjectManager()->get(
            Indexer::class
        );
        $this->_state = Bootstrap::getObjectManager()->get(
            State::class
        );
    }
}

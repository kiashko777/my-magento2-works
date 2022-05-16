<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRule\Model;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    /**
     * @var Rule
     */
    protected $_object;

    /**
     * @magentoAppIsolation enabled
     * @covers \Magento\CatalogRule\Model\Rule::calcProductPriceRule
     */
    public function testCalcProductPriceRule()
    {
        $product = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $this->assertEquals($this->_object->calcProductPriceRule($product, 100), 45);
        $product->setParentId(true);
        $this->assertEquals($this->_object->calcProductPriceRule($product, 50), 50);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $resourceMock = $this->createPartialMock(
            \Magento\CatalogRule\Model\ResourceModel\Rule::class,
            ['getIdFieldName', 'getRulesFromProduct']
        );
        $resourceMock->expects($this->any())->method('getIdFieldName')->willReturn('id');
        $resourceMock->expects(
            $this->any()
        )->method(
            'getRulesFromProduct'
        )->willReturn(
            $this->_getCatalogRulesFixtures()
        );

        $this->_object = Bootstrap::getObjectManager()->create(
            Rule::class,
            ['ruleResourceModel' => $resourceMock]
        );
    }

    /**
     * Get array with catalog rule data
     *
     * @return array
     */
    protected function _getCatalogRulesFixtures()
    {
        return [
            [
                'action_operator' => 'by_percent',
                'action_amount' => '50.0000',
                'action_stop' => '0'
            ],
            [
                'action_operator' => 'by_percent',
                'action_amount' => '10.0000',
                'action_stop' => '0'
            ]
        ];
    }
}

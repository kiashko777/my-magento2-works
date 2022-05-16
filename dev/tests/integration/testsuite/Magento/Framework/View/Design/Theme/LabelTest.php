<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Design\Theme;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\ResourceModel\Theme\Collection;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    /**
     * @var Label
     */
    protected $_model;

    /**
     * @covers \Magento\Framework\View\Design\Theme\Label::getLabelsCollection
     */
    public function testGetLabelsCollection()
    {
        /** @var $expectedCollection Collection */
        $expectedCollection = Bootstrap::getObjectManager()->create(
            \Magento\Framework\View\Design\Theme\Label\ListInterface::class
        );

        $expectedItemsCount = count($expectedCollection->getLabels());

        $labelsCollection = $this->_model->getLabelsCollection();
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $this->_model->getLabelsCollection('-- Please Select --');
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Label::class
        );
    }
}

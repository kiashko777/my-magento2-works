<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class SamplesTest
 *
 * @package Magento\Downloadable\Block\Adminhtml\Catalog\Products\Edit\Tab\Downloadable
 * @deprecated
 * @see \Magento\Downloadable\Ui\DataProvider\Products\Form\Modifier\Samples
 */
class SamplesTest extends TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Samples::class
        );
        LinksTest::performUploadButtonTest(
            $block
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSampleData()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(
            Registry::class
        )->register(
            'current_product',
            new DataObject(['type_id' => 'simple'])
        );
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Samples::class
        );
        $this->assertEmpty($block->getSampleData());
    }

    /**
     * Get Samples Title for simple/virtual/downloadable product
     *
     * @magentoConfigFixture current_store catalog/downloadable/samples_title Samples Title Test
     * @magentoAppIsolation enabled
     * @dataProvider productSamplesTitleDataProvider
     *
     * @param string $productType
     * @param string $samplesTitle
     * @param string $expectedResult
     */
    public function testGetSamplesTitle($productType, $samplesTitle, $expectedResult)
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(
            Registry::class
        )->register(
            'current_product',
            new DataObject(
                [
                    'type_id' => $productType,
                    'id' => '1',
                    'samples_title' => $samplesTitle,
                ]
            )
        );
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Samples::class
        );
        $this->assertEquals($expectedResult, $block->getSamplesTitle());
    }

    /**
     * Data Provider with product types
     *
     * @return array
     */
    public function productSamplesTitleDataProvider()
    {
        return [
            ['simple', null, 'Samples Title Test'],
            ['simple', 'Samples Title', 'Samples Title Test'],
            ['virtual', null, 'Samples Title Test'],
            ['virtual', 'Samples Title', 'Samples Title Test'],
            ['downloadable', null, null],
            ['downloadable', 'Samples Title', 'Samples Title']
        ];
    }
}

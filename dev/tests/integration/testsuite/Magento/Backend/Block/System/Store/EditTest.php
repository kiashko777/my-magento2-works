<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\System\Store;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Block\System\Store\Edit\Form\Group;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class EditTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @param $registryData
     * @param $expected
     * @dataProvider getStoreTypesForLayout
     */
    public function testStoreTypeFormCreated($registryData, $expected)
    {
        $this->_initStoreTypesInRegistry($registryData);

        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Edit */
        $block = $layout->createBlock(Edit::class, 'block');
        $block->setArea(FrontNameResolver::AREA_CODE);

        $this->assertInstanceOf($expected, $block->getChildBlock('form'));
    }

    /**
     * @param $registryData
     */
    protected function _initStoreTypesInRegistry($registryData)
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            if ($key == 'store_data') {
                $value = Bootstrap::getObjectManager()->create($value);
            }
            $objectManager->get(Registry::class)->register($key, $value);
        }
    }

    /**
     * @return array
     */
    public function getStoreTypesForLayout()
    {
        return [
            [
                ['store_type' => 'website', 'store_data' => Website::class],
                \Magento\Backend\Block\System\Store\Edit\Form\Website::class,
            ],
            [
                ['store_type' => 'group', 'store_data' => \Magento\Store\Model\Store::class],
                Group::class
            ],
            [
                ['store_type' => 'store', 'store_data' => \Magento\Store\Model\Store::class],
                \Magento\Backend\Block\System\Store\Edit\Form\Store::class
            ]
        ];
    }

    /**
     * @magentoAppIsolation enabled
     * @param $registryData
     * @param $expected
     * @dataProvider getStoreDataForBlock
     */
    public function testGetHeaderText($registryData, $expected)
    {
        $this->_initStoreTypesInRegistry($registryData);

        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Edit */
        $block = $layout->createBlock(Edit::class, 'block');
        $block->setArea(FrontNameResolver::AREA_CODE);

        $this->assertEquals($expected, $block->getHeaderText());
    }

    /**
     * @return array
     */
    public function getStoreDataForBlock()
    {
        return [
            [
                [
                    'store_type' => 'website',
                    'store_data' => Website::class,
                    'store_action' => 'add',
                ],
                'New Web Site',
            ],
            [
                [
                    'store_type' => 'website',
                    'store_data' => Website::class,
                    'store_action' => 'edit',
                ],
                'Edit Web Site'
            ],
            [
                ['store_type' => 'group', 'store_data' => \Magento\Store\Model\Store::class, 'store_action' => 'add'],
                'New Store'
            ],
            [
                ['store_type' => 'group', 'store_data' => \Magento\Store\Model\Store::class, 'store_action' => 'edit'],
                'Edit Store'
            ],
            [
                ['store_type' => 'store', 'store_data' => \Magento\Store\Model\Store::class, 'store_action' => 'add'],
                'New Store View'
            ],
            [
                ['store_type' => 'store', 'store_data' => \Magento\Store\Model\Store::class, 'store_action' => 'edit'],
                'Edit Store View'
            ]
        ];
    }

    protected function tearDown(): void
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->unregister('store_type');
        $objectManager->get(Registry::class)->unregister('store_data');
        $objectManager->get(Registry::class)->unregister('store_action');
    }
}

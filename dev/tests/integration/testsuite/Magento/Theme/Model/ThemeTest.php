<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model;

use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    /**
     * Test crud operations for theme model using valid data
     *
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        /** @var $themeModel ThemeInterface */
        $themeModel = Bootstrap::getObjectManager()->create(
            ThemeInterface::class
        );
        $themeModel->setData($this->_getThemeValidData());

        $crud = new Entity($themeModel, []);
        $crud->testCrud();
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getThemeValidData()
    {
        return [
            'area' => 'space_area',
            'theme_title' => 'Space theme',
            'parent_id' => null,
            'is_featured' => false,
            'theme_path' => 'default/space',
            'preview_image' => 'images/preview.png',
            'type' => ThemeInterface::TYPE_VIRTUAL
        ];
    }

    /**
     * Test theme on child relations
     */
    public function testChildRelation()
    {
        /** @var $theme ThemeInterface */
        $theme = Bootstrap::getObjectManager()->get(
            ThemeInterface::class
        );
        $collection = $theme->getCollection()
            ->addTypeFilter(ThemeInterface::TYPE_VIRTUAL);
        /** @var $currentTheme ThemeInterface */
        foreach ($collection as $currentTheme) {
            $parentTheme = $currentTheme->getParentTheme();
            if (!empty($parentTheme)) {
                $this->assertTrue($parentTheme->hasChildThemes());
            }
        }
    }

    /**
     * @magentoComponentsDir Magento/Theme/Model/_files/design
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testGetInheritedThemes()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Registration $registration */
        $registration = $objectManager->get(
            Registration::class
        );
        $registration->register();
        /** @var FlyweightFactory $themeFactory */
        $themeFactory = $objectManager->get(
            FlyweightFactory::class
        );
        $theme = $themeFactory->create('Vendor_FrameworkThemeTest/custom_theme');
        $this->assertCount(2, $theme->getInheritedThemes());
        $expected = [];
        foreach ($theme->getInheritedThemes() as $someTheme) {
            $expected[] = $someTheme->getFullPath();
        }
        $this->assertEquals(
            ['frontend/Vendor_FrameworkThemeTest/default', 'frontend/Vendor_FrameworkThemeTest/custom_theme'],
            $expected
        );
    }
}

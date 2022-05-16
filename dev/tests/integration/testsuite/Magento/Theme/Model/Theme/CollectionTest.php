<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test for filesystem themes collection
 */

namespace Magento\Theme\Model\Theme;

use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoComponentsDir Magento/Theme/Model/_files/design
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_model;

    /**
     * Test load themes collection from filesystem
     *
     * @magentoAppIsolation enabled
     */
    public function testLoadThemesFromFileSystem()
    {
        $this->_model->addConstraint(Collection::CONSTRAINT_AREA, 'frontend');
        $this->assertNotEmpty($this->_model->getItemById('frontend/Magento_FrameworkThemeTest/default'));
        $this->assertEmpty($this->_model->getItemById('Adminhtml/FrameworkThemeTest/test'));
    }

    /**
     * Load from configuration
     *
     * @dataProvider expectedThemeDataFromConfiguration
     */
    public function testLoadFromConfiguration($area, $vendor, $themeName, $expectedData)
    {
        $this->_model->addConstraint(Collection::CONSTRAINT_AREA, $area);
        $this->_model->addConstraint(Collection::CONSTRAINT_VENDOR, $vendor);
        $this->_model->addConstraint(Collection::CONSTRAINT_THEME_NAME, $themeName);
        $theme = $this->_model->getFirstItem();
        $this->assertEquals($expectedData, $theme->getData());
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    public function expectedThemeDataFromConfiguration()
    {
        return [
            [
                'frontend', 'Magento_FrameworkThemeTest', 'default',
                [
                    'area' => 'frontend',
                    'theme_title' => 'Default',
                    'parent_id' => null,
                    'parent_theme_path' => null,
                    'theme_path' => 'Magento_FrameworkThemeTest/default',
                    'code' => 'Magento_FrameworkThemeTest/default',
                    'preview_image' => null,
                    'type' => ThemeInterface::TYPE_PHYSICAL,
                ],
            ]
        ];
    }

    /**
     * Test if theme present in file system
     *
     * @magentoAppIsolation enabled
     * @covers \Magento\Theme\Model\Theme\Collection::hasTheme
     */
    public function testHasThemeInCollection()
    {
        /** @var $themeModel ThemeInterface */
        $themeModel = Bootstrap::getObjectManager()->create(
            ThemeInterface::class
        );
        $themeModel->setData(
            [
                'area' => 'space_area',
                'theme_title' => 'Space theme',
                'parent_id' => null,
                'is_featured' => false,
                'theme_path' => 'default_space',
                'preview_image' => 'images/preview.png',
                'type' => ThemeInterface::TYPE_PHYSICAL,
            ]
        );

        $this->_model->addConstraint(Collection::CONSTRAINT_AREA, Area::AREA_FRONTEND);
        $this->assertFalse($this->_model->hasTheme($themeModel));
    }

    protected function setUp(): void
    {
        $directoryList = Bootstrap::getObjectManager()->create(
            DirectoryList::class,
            [
                'root' => DirectoryList::ROOT,
            ]
        );
        $filesystem = Bootstrap::getObjectManager()->create(
            Filesystem::class,
            ['directoryList' => $directoryList]
        );
        $this->_model = Bootstrap::getObjectManager()->create(
            Collection::class,
            ['filesystem' => $filesystem]
        );
    }
}

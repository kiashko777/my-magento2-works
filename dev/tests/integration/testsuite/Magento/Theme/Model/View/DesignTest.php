<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\View;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Config\View;
use Magento\Framework\View\ConfigInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\FileSystem;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

/**
 * @magentoComponentsDir Magento/Theme/Model/_files/design
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DesignTest extends TestCase
{
    /**
     * @var DesignInterface
     */
    protected $_model;

    /**
     * @var FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var ConfigInterface
     */
    protected $_viewConfig;

    public static function setUpBeforeClass(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\Framework\Filesystem $filesystem */
        $filesystem = $objectManager->get(\Magento\Framework\Filesystem::class);
        $themeDir = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $themeDir->delete('theme/frontend');
        $themeDir->delete('theme/_merged');

        $libDir = $filesystem->getDirectoryWrite(DirectoryList::LIB_WEB);
        $libDir->copyFile('prototype/prototype.js', 'prototype/prototype.min.js');
    }

    public static function tearDownAfterClass(): void
    {
        /** @var \Magento\Framework\Filesystem $filesystem */
        $filesystem = Bootstrap::getObjectManager()
            ->get(\Magento\Framework\Filesystem::class);
        $libDir = $filesystem->getDirectoryWrite(DirectoryList::LIB_WEB);
        $libDir->delete('prototype/prototype.min.js');
    }

    public function testSetGetArea()
    {
        $this->assertEquals(DesignInterface::DEFAULT_AREA, $this->_model->getArea());
        Bootstrap::getObjectManager()->get(State::class)
            ->setAreaCode(Area::AREA_ADMINHTML);
        $this->assertEquals(Area::AREA_ADMINHTML, $this->_model->getArea());
    }

    public function testSetDesignTheme()
    {
        $this->_model->setDesignTheme('Magento/blank', 'frontend');
        $this->assertEquals('Magento/blank', $this->_model->getDesignTheme()->getThemePath());
    }

    public function testGetDesignTheme()
    {
        $this->assertInstanceOf(ThemeInterface::class, $this->_model->getDesignTheme());
    }

    /**
     * @magentoConfigFixture current_store design/theme/theme_id 0
     */
    public function testGetConfigurationDesignThemeDefaults()
    {
        $objectManager = Bootstrap::getObjectManager();

        $themes = ['frontend' => 'test_f', 'Adminhtml' => 'test_a'];
        $design = $objectManager->create(Design::class, ['themes' => $themes]);
        $objectManager->addSharedInstance($design, Design::class);

        $model = $objectManager->get(Design::class);

        $this->assertEquals('test_f', $model->getConfigurationDesignTheme());
        $this->assertEquals('test_f', $model->getConfigurationDesignTheme('frontend'));
        $this->assertEquals('test_f', $model->getConfigurationDesignTheme('frontend', ['store' => 0]));
        $this->assertEquals('test_f', $model->getConfigurationDesignTheme('frontend', ['store' => null]));
        $this->assertEquals('test_a', $model->getConfigurationDesignTheme('Adminhtml'));
        $this->assertEquals('test_a', $model->getConfigurationDesignTheme('Adminhtml', ['store' => uniqid()]));
    }

    /**
     * @magentoConfigFixture current_store design/theme/theme_id one
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     */
    public function testGetConfigurationDesignThemeStore()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var MutableScopeConfigInterface $mutableConfig */
        $mutableConfig = $objectManager->get(MutableScopeConfigInterface::class);
        $mutableConfig->setValue('design/theme/theme_id', 'two', ScopeInterface::SCOPE_STORE, 'fixturestore');

        $storeId = $objectManager->get(StoreManagerInterface::class)
            ->getStore()
            ->getId();
        $this->assertEquals('one', $this->_model->getConfigurationDesignTheme());
        $this->assertEquals('one', $this->_model->getConfigurationDesignTheme(null, ['store' => $storeId]));
        $this->assertEquals('one', $this->_model->getConfigurationDesignTheme('frontend', ['store' => $storeId]));
        $this->assertEquals('two', $this->_model->getConfigurationDesignTheme(null, ['store' => 'fixturestore']));
        $this->assertEquals(
            'two',
            $this->_model->getConfigurationDesignTheme('frontend', ['store' => 'fixturestore'])
        );
    }

    /**
     * @dataProvider getFilenameDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetFilename($file, $params)
    {
        $this->_emulateFixtureTheme();
        $this->assertFileExists($this->_viewFileSystem->getFilename($file, $params));
    }

    /**
     * Emulate fixture design theme
     *
     * @param string $themePath
     */
    protected function _emulateFixtureTheme($themePath = 'Test_FrameworkThemeTest/default')
    {
        Bootstrap::getInstance()->loadArea('frontend');
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(DesignInterface::class)->setDesignTheme($themePath);

        $this->_viewFileSystem = $objectManager->create(FileSystem::class);
        $this->_viewConfig = $objectManager->create(ConfigInterface::class);
    }

    /**
     * @return array
     */
    public function getFilenameDataProvider()
    {
        return [
            ['theme_file.txt', ['module' => 'Magento_Catalog']],
            ['Magento_Catalog::theme_file.txt', []],
            ['Magento_Catalog::theme_file_with_2_dots..txt', []],
            ['Magento_Catalog::theme_file.txt', ['module' => 'Overridden_Module']]
        ];
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetViewConfig()
    {
        $this->_emulateFixtureTheme();
        $config = $this->_viewConfig->getViewConfig();
        $this->assertInstanceOf(View::class, $config);
        $this->assertEquals(['var1' => 'value1', 'var2' => 'value2'], $config->getVars('Namespace_Module'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetConfigCustomized()
    {
        $this->_emulateFixtureTheme();
        /** @var $theme ThemeInterface */
        $theme = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->getDesignTheme();
        $customConfigFile = $theme->getCustomization()->getCustomViewConfigPath();
        /** @var $filesystem \Magento\Framework\Filesystem */
        $filesystem = Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Filesystem::class);
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $relativePath = $directory->getRelativePath($customConfigFile);
        try {
            $directory->writeFile(
                $relativePath,
                '<?xml version="1.0" encoding="UTF-8"?>
                <view><vars  module="Namespace_Module"><var name="customVar">custom value</var></vars></view>'
            );

            $config = $this->_viewConfig->getViewConfig();
            $this->assertInstanceOf(View::class, $config);
            $this->assertEquals(['customVar' => 'custom value'], $config->getVars('Namespace_Module'));
        } catch (Exception $e) {
            $directory->delete($relativePath);
            throw $e;
        }
        $directory->delete($relativePath);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Registration $registration */
        $registration = $objectManager->get(
            Registration::class
        );
        $registration->register();
        $this->_model = $objectManager->create(DesignInterface::class);
        $this->_viewFileSystem = $objectManager->create(FileSystem::class);
        $this->_viewConfig = $objectManager->create(ConfigInterface::class);
        $objectManager->get(State::class)->setAreaCode('frontend');
    }
}

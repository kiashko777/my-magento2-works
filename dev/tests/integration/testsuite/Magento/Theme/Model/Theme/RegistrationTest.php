<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Theme;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\State;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme;
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
{
    /**
     * @var Registration
     */
    protected $_model;

    /**
     * @var Theme
     */
    protected $_theme;

    public static function setUpBeforeClass(): void
    {
        ComponentRegistrar::register(
            ComponentRegistrar::THEME,
            'frontend/Test/test_theme',
            dirname(__DIR__) . '/_files/design/frontend/Test/test_theme'
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testVirtualByVirtualRelation()
    {
        $this->registerThemes();
        $theme = $this->_getTestTheme();

        $virtualTheme = clone $this->_theme;
        $virtualTheme->setData($theme->getData())->setId(null);
        $virtualTheme->setType(ThemeInterface::TYPE_VIRTUAL)->save();

        $subVirtualTheme = clone $this->_theme;
        $subVirtualTheme->setData($theme->getData())->setId(null);
        $subVirtualTheme->setParentId(
            $virtualTheme->getId()
        )->setType(
            ThemeInterface::TYPE_VIRTUAL
        )->save();

        $this->registerThemes();
        $parentId = $subVirtualTheme->getParentId();
        $subVirtualTheme->load($subVirtualTheme->getId());
        $this->assertNotEquals($parentId, $subVirtualTheme->getParentId());
    }

    /**
     * Register themes
     * Use this method only with database isolation
     *
     * @return RegistrationTest
     */
    protected function registerThemes()
    {
        $this->_model->register();
        return $this;
    }

    /**
     * Use this method only with database isolation
     *
     * @return Theme
     */
    protected function _getTestTheme()
    {
        $theme = $this->_theme->getCollection()->getThemeByFullPath(
            implode(ThemeInterface::PATH_SEPARATOR, ['frontend', 'Test/test_theme'])
        );
        $this->assertNotEmpty($theme->getId());
        return $theme;
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testPhysicalThemeElimination()
    {
        $this->registerThemes();
        $theme = $this->_getTestTheme();

        $testTheme = clone $this->_theme;
        $testTheme->setData($theme->getData())->setThemePath('empty')->setId(null);
        $testTheme->setType(ThemeInterface::TYPE_PHYSICAL)->save();

        $this->registerThemes();
        $testTheme->load($testTheme->getId());
        $this->assertNotEquals(
            (int)$testTheme->getType(),
            ThemeInterface::TYPE_PHYSICAL
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testRegister()
    {
        $this->registerThemes();
        $themePath = implode(
            ThemeInterface::PATH_SEPARATOR,
            ['frontend', 'Test/test_theme']
        );
        $theme = $this->_model->getThemeFromDb($themePath);
        $this->assertEquals($themePath, $theme->getFullPath());
    }

    /**
     * Initialize base models
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(AreaList::class)
            ->getArea(FrontNameResolver::AREA_CODE)
            ->load(Area::PART_CONFIG);

        $objectManager->get(State::class)
            ->setAreaCode(FrontNameResolver::AREA_CODE);
        $this->_theme = $objectManager
            ->create(ThemeInterface::class);
        $this->_model = $objectManager
            ->create(Registration::class);
    }
}

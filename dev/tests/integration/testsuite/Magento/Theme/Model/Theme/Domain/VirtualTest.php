<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Theme\Domain;

use Magento\Framework\App\Area;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class VirtualTest extends TestCase
{
    /**
     * @var array
     */
    protected $_themes = [
        'physical' => [
            'parent_id' => null,
            'theme_path' => 'test/test',
            'theme_title' => 'Test physical theme',
            'area' => Area::AREA_FRONTEND,
            'type' => ThemeInterface::TYPE_PHYSICAL,
            'code' => 'physical',
        ],
        'virtual' => [
            'parent_id' => null,
            'theme_path' => '',
            'theme_title' => 'Test virtual theme',
            'area' => Area::AREA_FRONTEND,
            'type' => ThemeInterface::TYPE_VIRTUAL,
            'code' => 'virtual',
        ],
        'staging' => [
            'parent_id' => null,
            'theme_path' => '',
            'theme_title' => 'Test staging theme',
            'area' => Area::AREA_FRONTEND,
            'type' => ThemeInterface::TYPE_STAGING,
            'code' => 'staging',
        ],
    ];

    /**
     * @var int
     */
    protected $_physicalThemeId;

    /**
     * @var int
     */
    protected $_virtualThemeId;

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetPhysicalTheme()
    {
        $objectManager = Bootstrap::getObjectManager();
        //1. set up fixture
        /** @var $physicalTheme ThemeInterface */
        $physicalTheme = $objectManager->create(ThemeInterface::class);
        $physicalTheme->setData($this->_themes['physical']);
        $physicalTheme->save();

        $this->_themes['virtual']['parent_id'] = $physicalTheme->getId();

        /** @var $virtualTheme ThemeInterface */
        $virtualTheme = $objectManager->create(ThemeInterface::class);
        $virtualTheme->setData($this->_themes['virtual']);
        $virtualTheme->save();

        $this->_themes['staging']['parent_id'] = $virtualTheme->getId();

        /** @var $stagingTheme ThemeInterface */
        $stagingTheme = $objectManager->create(ThemeInterface::class);
        $stagingTheme->setData($this->_themes['staging']);
        $stagingTheme->save();

        $this->_physicalThemeId = $physicalTheme->getId();
        $this->_virtualThemeId = $virtualTheme->getId();

        //2. run test
        /** @var $virtualTheme ThemeInterface */
        $virtualTheme = $objectManager->create(ThemeInterface::class);
        $virtualTheme->load($this->_virtualThemeId);

        $this->assertEquals(
            $this->_physicalThemeId,
            $virtualTheme->getDomainModel(
                ThemeInterface::TYPE_VIRTUAL
            )->getPhysicalTheme()->getId()
        );
    }

    protected function tearDown(): void
    {
        $this->_physicalThemeId = null;
        $this->_virtualThemeId = null;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App;

use Laminas\Stdlib\Parameters;
use Magento\TestFramework\Request;
use Magento\Theme\Model\Design;
use PHPUnit\Framework\TestCase;

class AreaTest extends TestCase
{
    /**
     * @var Area
     */
    protected $_model;

    public static function tearDownAfterClass(): void
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(CacheInterface::class)
            ->clean([Design::CACHE_TAG]);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitDesign()
    {
        $defaultTheme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\DesignInterface::class
        )->setDefaultDesignTheme()->getDesignTheme();
        $this->_model->load(Area::PART_DESIGN);
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\DesignInterface::class
        )->setDefaultDesignTheme();

        $this->assertEquals($defaultTheme->getThemePath(), $design->getDesignTheme()->getThemePath());
        $this->assertEquals('frontend', $design->getArea());

        // try second time and make sure it won't load second time
        $this->_model->load(Area::PART_DESIGN);
        $designArea = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\DesignInterface::class
        )->getArea();
        $sameDesign = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\DesignInterface::class
        )->setArea(
            $designArea
        );
        $this->assertSame($design, $sameDesign);
    }


    public function testDetectDesignUserAgent()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->create(Request::class);
        $request->setServer(new Parameters(['HTTP_USER_AGENT' => 'Mozilla Firefox']));
        $this->_model->detectDesign($request);
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\DesignInterface::class
        );
        $this->assertEquals('Magento/blank', $design->getDesignTheme()->getThemePath());
    }

    // @codingStandardsIgnoreStart
    /**
     * @magentoConfigFixture current_store design/theme/ua_regexp {"_":{"regexp":"\/firefox\/i","value":"Magento\/blank"}}
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd

    public function testDetectDesignDesignChange()
    {
        $this->_model->detectDesign();
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\DesignInterface::class
        );
        $this->assertEquals('Magento/luma', $design->getDesignTheme()->getThemePath());
    }

    // @codingStandardsIgnoreStart
    /**
     * @magentoConfigFixture current_store design/theme/ua_regexp {"_":{"regexp":"\/firefox\/i","value":"Magento\/blank"}}
     * @magentoDataFixture Magento/Theme/_files/design_change.php
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd

    public function testDetectDesignNonFrontend()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $model = $objectManager->create(Area::class, ['areaCode' => 'Adminhtml']);
        /** @var $request Request */
        $request = $objectManager->create(Request::class);
        $request->setServer(new Parameters(['HTTP_USER_AGENT' => 'Mozilla Firefox']));
        $model->detectDesign($request);
        $design = $objectManager->get(\Magento\Framework\View\DesignInterface::class);
        $this->assertNotEquals('Magento/blank', $design->getDesignTheme()->getThemePath());
    }

    // @codingStandardsIgnoreStart
    /**
     * Test that non-frontend areas are not affected neither by user-agent reg expressions, nor by the "design change"
     *
     * @magentoConfigFixture current_store design/theme/ua_regexp {"_":{"regexp":"\/firefox\/i","value":"Magento\/blank"}}
     * magentoDataFixture Magento/Theme/_files/design_change.php
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    protected function setUp(): void
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(State::class)
            ->setAreaCode('frontend');
        /** @var $_model Area */
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            Area::class,
            ['areaCode' => 'frontend']
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View;

use Magento\Framework\App\State;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the view layer fallback mechanism
 * @magentoComponentsDir Magento/Theme/Model/_files/design
 * @magentoDbIsolation enabled
 */
class FileSystemTest extends TestCase
{
    /**
     * @var FileSystem
     */
    protected $_model = null;

    public function testGetTemplateFileName()
    {
        $expected = '%s/frontend/Test/default/Magento_Catalog/templates/theme_template.phtml';
        $actual = $this->_model->getTemplateFileName('Magento_Catalog::theme_template.phtml', []);
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    /**
     * Tests expected vs actual found fallback filename
     *
     * @param string $expected
     * @param string $actual
     */
    protected function _testExpectedVersusActualFilename($expected, $actual)
    {
        $this->assertStringMatchesFormat($expected, $actual);
        $this->assertFileExists($actual);
    }

    public function testGetFileNameAccordingToLocale()
    {
        $expected = '%s/frontend/Test/default/web/i18n/fr_FR/logo.gif';
        $actual = $this->_model->getStaticFileName('logo.gif', ['locale' => 'fr_FR']);
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    /**
     * @magentoComponentsDir Magento/Framework/View/_files/Fixture_Module
     */
    public function testGetViewFile()
    {
        $expected = '%s/frontend/Vendor/custom_theme/Fixture_Module/web/fixture_script.js';
        $params = ['theme' => 'Vendor_FrameworkThemeTest/custom_theme'];
        $actual = $this->_model->getStaticFileName('Fixture_Module::fixture_script.js', $params);
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Registration $registration */
        $registration = $objectManager->get(
            Registration::class
        );
        $registration->register();
        $objectManager->get(State::class)->setAreaCode('frontend');
        $this->_model = $objectManager->create(
            FileSystem::class
        );
        $objectManager->get(
            DesignInterface::class
        )->setDesignTheme(
            'Test_FrameworkThemeTest/default'
        );
    }
}

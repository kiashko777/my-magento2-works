<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Design\Fallback;

use InvalidArgumentException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\ResourceModel\Theme\Collection;
use Magento\Theme\Model\Theme;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

/**
 * Factory Test
 * @magentoComponentsDir Magento/Framework/View/_files/fallback
 * @magentoDbIsolation enabled
 */
class RulePoolTest extends TestCase
{
    /**
     * @var RulePool
     */
    protected $model;

    /**
     * @var array
     */
    protected $defaultParams;

    public function testGetRuleUnsupportedType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fallback rule \'unsupported_type\' is not supported');

        $this->model->getRule('unsupported_type');
    }

    /**
     * @param string $type
     * @param array $overriddenParams
     * @param string $expectedErrorMessage
     *
     * @dataProvider getPatternDirsExceptionDataProvider
     */
    public function testGetPatternDirsException($type, array $overriddenParams, $expectedErrorMessage)
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage($expectedErrorMessage);
        $params = $overriddenParams + $this->defaultParams;
        $this->model->getRule($type)->getPatternDirs($params);
    }

    /**
     * @return array
     */
    public function getPatternDirsExceptionDataProvider()
    {
        $exceptions = [
            'no theme' => [
                ['theme' => null],
                'Parameter "theme" should be specified and should implement the theme interface',
            ],
            'no area' => [
                ['area' => null],
                "Required parameter 'area' was not passed",
            ],
        ];
        $exceptionsPerTypes = [
            RulePool::TYPE_LOCALE_FILE => [
                'no theme',
            ],
            RulePool::TYPE_FILE => [
                'no theme', 'no area',
            ],
            RulePool::TYPE_TEMPLATE_FILE => [
                'no theme', 'no area',
            ],
            RulePool::TYPE_STATIC_FILE => [
                'no theme', 'no area',
            ],
        ];

        $data = [];
        foreach ($exceptionsPerTypes as $type => $exceptionKeys) {
            foreach ($exceptionKeys as $key) {
                $data[$type . ', ' . $key] = [$type, $exceptions[$key][0], $exceptions[$key][1]];
            }
        }

        return $data;
    }

    /**
     * @param string $type
     * @param array $overriddenParams
     * @param array $expectedResult
     *
     * @dataProvider getPatternDirsDataProvider
     */
    public function testGetPatternDirs($type, array $overriddenParams, array $expectedResult)
    {
        $actualResult = $this->model->getRule($type)
            ->getPatternDirs($overriddenParams + $this->defaultParams);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getPatternDirsDataProvider()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var ComponentRegistrarInterface $componentRegistrar */
        $componentRegistrar = $objectManager->get(
            ComponentRegistrarInterface::class
        );
        $coreModulePath = $componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Magento_Theme');
        /** @var Filesystem $filesystem */
        $filesystem = $objectManager->get(Filesystem::class);
        $libPath = rtrim($filesystem->getDirectoryRead(DirectoryList::LIB_WEB)->getAbsolutePath(), '/');

        $themeOnePath = BP . '/dev/tests/integration/testsuite/Magento/Framework/View/_files/fallback/design/frontend/'
            . 'Vendor/custom_theme';
        $themeTwoPath = BP . '/dev/tests/integration/testsuite/Magento/Framework/View/_files/fallback/design/frontend/'
            . 'Vendor/default';
        $modulePath = BP . '/dev/tests/integration/testsuite/Magento/Framework/View/_files/fallback/app/code/'
            . 'ViewTest_Module';

        return [
            'locale' => [
                RulePool::TYPE_LOCALE_FILE,
                [],
                [
                    $themeOnePath,
                    $themeTwoPath,
                ],
            ],
            'file, modular' => [
                RulePool::TYPE_FILE,
                [],
                [
                    $themeOnePath . '/ViewTest_Module',
                    $themeTwoPath . '/ViewTest_Module',
                    $modulePath . '/view/area',
                    $modulePath . '/view/base',
                ],
            ],
            'file, non-modular' => [
                RulePool::TYPE_FILE,
                ['namespace' => null, 'module_name' => null],
                [
                    $themeOnePath,
                    $themeTwoPath,
                ],
            ],

            'template, modular' => [
                RulePool::TYPE_TEMPLATE_FILE,
                [],
                [
                    $themeOnePath . '/ViewTest_Module/templates',
                    $themeTwoPath . '/ViewTest_Module/templates',
                    $modulePath . '/view/area/templates',
                    $modulePath . '/view/base/templates',
                ],
            ],
            'template, non-modular' => [
                RulePool::TYPE_TEMPLATE_FILE,
                ['namespace' => null, 'module_name' => null],
                [
                    $themeOnePath . '/templates',
                    $themeTwoPath . '/templates',
                ],
            ],
            'template, non-modular-magento-core' => [
                RulePool::TYPE_TEMPLATE_FILE,
                ['module_name' => 'Magento_Theme'],
                [
                    $themeOnePath . '/Magento_Theme/templates',
                    $themeTwoPath . '/Magento_Theme/templates',
                    $coreModulePath . '/view/area/templates',
                    $coreModulePath . '/view/base/templates',
                ],
            ],

            'view, modular localized' => [
                RulePool::TYPE_STATIC_FILE,
                [],
                [
                    $themeOnePath . '/ViewTest_Module/web/i18n/en_US',
                    $themeOnePath . '/ViewTest_Module/web',
                    $themeTwoPath . '/ViewTest_Module/web/i18n/en_US',
                    $themeTwoPath . '/ViewTest_Module/web',
                    $modulePath . '/view/area/web/i18n/en_US',
                    $modulePath . '/view/base/web/i18n/en_US',
                    $modulePath . '/view/area/web',
                    $modulePath . '/view/base/web',
                ],
            ],
            'view, modular non-localized' => [
                RulePool::TYPE_STATIC_FILE,
                ['locale' => null],
                [
                    $themeOnePath . '/ViewTest_Module/web',
                    $themeTwoPath . '/ViewTest_Module/web',
                    $modulePath . '/view/area/web',
                    $modulePath . '/view/base/web',
                ],
            ],
            'view, non-modular localized' => [
                RulePool::TYPE_STATIC_FILE,
                ['module_name' => null],
                [
                    $themeOnePath . '/web/i18n/en_US',
                    $themeOnePath . '/web',
                    $themeTwoPath . '/web/i18n/en_US',
                    $themeTwoPath . '/web',
                    $libPath,
                ],
            ],
            'view, non-modular non-localized' => [
                RulePool::TYPE_STATIC_FILE,
                ['module_name' => null, 'locale' => null],
                [
                    $themeOnePath . '/web',
                    $themeTwoPath . '/web',
                    $libPath,
                ],
            ],
            // Single test, as emails will always be loaded in a modular context with no locale specificity
            'email' => [
                RulePool::TYPE_EMAIL_TEMPLATE,
                [],
                [
                    $themeOnePath . '/ViewTest_Module/email',
                    $themeTwoPath . '/ViewTest_Module/email',
                    $modulePath . '/view/area/email',
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Registration $registration */
        $registration = $objectManager->get(
            Registration::class
        );
        $registration->register();
        $this->model = $objectManager->create(RulePool::class);
        /** @var Collection $collection */
        $collection = $objectManager->create(Collection::class);
        /** @var Theme $theme */
        $theme = $collection->getThemeByFullPath('frontend/Vendor_ViewTest/custom_theme');

        $this->defaultParams = [
            'area' => 'area',
            'theme' => $theme,
            'module_name' => 'ViewTest_Module',
            'locale' => 'en_US',
        ];
    }

    protected function tearDown(): void
    {
        $this->model = null;
        $this->defaultParams = [];
    }
}

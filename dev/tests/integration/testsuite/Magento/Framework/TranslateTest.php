<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Phrase\Renderer\Composite;
use Magento\Framework\Phrase\RendererInterface;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\Theme\Model\ThemeFactory;
use Magento\Theme\Model\View\Design;
use Magento\Theme\Model\View\Design\Proxy;
use PHPUnit\Framework\MockObject\MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoCache all disabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TranslateTest extends TestCase
{
    /**
     * @var Translate
     */
    private $translate;

    public function testLoadData()
    {
        $data = $this->translate->loadData(null, true)->getData();
        $this->translate->loadData()->getData();
        $dataCached = $this->translate->loadData()->getData();
        $this->assertEquals($data, $dataCached);
    }

    /**
     * @magentoCache all disabled
     * @dataProvider translateDataProvider
     *
     * @param string $inputText
     * @param string $expectedTranslation
     * @return void
     * @throws Exception\LocalizedException
     */
    public function testTranslate($inputText, $expectedTranslation)
    {
        $this->translate->loadData(Area::AREA_FRONTEND);
        $actualTranslation = new Phrase($inputText);
        $this->assertEquals($expectedTranslation, $actualTranslation);
    }

    /**
     * @return array
     */
    public function translateDataProvider()
    {
        return [
            ['', ''],
            [
                'Theme phrase will be translated',
                'Theme phrase is translated',
            ],
            [
                'Phrase in Magento_Store module that doesn\'t need translation',
                'Phrase in Magento_Store module that doesn\'t need translation',
            ],
            [
                'Phrase in Magento_Catalog module that doesn\'t need translation',
                'Phrase in Magento_Catalog module that doesn\'t need translation',
            ],
            [
                'Magento_Store module phrase will be overridden by theme translation',
                'Magento_Store module phrase is overridden by theme translation',
            ],
            [
                'Magento_Catalog module phrase will be overridden by theme translation',
                'Magento_Catalog module phrase is overridden by theme translation',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        /** @var \Magento\Framework\View\FileSystem|MockObject $viewFileSystem */
        $viewFileSystem = $this->createPartialMock(
            \Magento\Framework\View\FileSystem::class,
            ['getLocaleFileName']
        );

        $viewFileSystem->expects($this->any())
            ->method('getLocaleFileName')
            ->willReturn(

                dirname(__DIR__) . '/Translation/Model/_files/Magento/design/Magento/theme/i18n/en_US.csv'

            );

        /** @var ThemeInterface|MockObject $theme */
        $theme = $this->createMock(ThemeInterface::class);
        $theme->expects($this->any())->method('getThemePath')->willReturn('Magento/luma');

        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($viewFileSystem, \Magento\Framework\View\FileSystem::class);

        /** @var $moduleReader Reader */
        $moduleReader = $objectManager->get(Reader::class);
        $moduleReader->setModuleDir(
            'Magento_Store',
            'i18n',
            dirname(__DIR__) . '/Translation/Model/_files/Magento/Store/i18n'
        );
        $moduleReader->setModuleDir(
            'Magento_Catalog',
            'i18n',
            dirname(__DIR__) . '/Translation/Model/_files/Magento/Catalog/i18n'
        );

        /** @var Design|MockObject $designModel */
        $designModel = $this->getMockBuilder(Design::class)
            ->setMethods(['getDesignTheme'])
            ->setConstructorArgs(
                [
                    $objectManager->get(StoreManagerInterface::class),
                    $objectManager->get(FlyweightFactory::class),
                    $objectManager->get(ScopeConfigInterface::class),
                    $objectManager->get(ThemeFactory::class),
                    $objectManager->get(ObjectManagerInterface::class),
                    $objectManager->get(State::class),
                    ['frontend' => 'Test/default']
                ]
            )
            ->getMock();

        $designModel->expects($this->any())->method('getDesignTheme')->willReturn($theme);

        $objectManager->addSharedInstance($designModel, Proxy::class);

        $this->translate = $objectManager->create(Translate::class);
        $objectManager->addSharedInstance($this->translate, Translate::class);
        $objectManager->removeSharedInstance(Composite::class);
        $objectManager->removeSharedInstance(\Magento\Framework\Phrase\Renderer\Translate::class);
        Phrase::setRenderer(
            $objectManager->get(RendererInterface::class)
        );
    }
}

<?php
/**
 * \Magento\Theme\Model\Layout\Config
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Layout;

use Magento\Framework\App\Cache;
use Magento\Framework\Config\FileResolverInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Layout\Config\Data;
use Magento\Theme\Model\Layout\Config\Reader;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $_model;

    public function testGetPageLayouts()
    {
        $empty = [
            'label' => 'Empty',
            'code' => 'empty',
        ];
        $oneColumn = [
            'label' => '1 column',
            'code' => '1column',
        ];
        $result = $this->_model->getPageLayouts();
        $this->assertEquals($empty, $result['empty']->getData());
        $this->assertEquals($oneColumn, $result['1column']->getData());
    }

    public function testGetPageLayout()
    {
        $empty = [
            'label' => 'Empty',
            'code' => 'empty',
        ];
        $this->assertEquals($empty, $this->_model->getPageLayout('empty')->getData());
        $this->assertFalse($this->_model->getPageLayout('unknownLayoutCode'));
    }

    public function testGetPageLayoutHandles()
    {
        $expected = ['empty' => 'empty', '1column' => '1column'];
        $this->assertEquals($expected, $this->_model->getPageLayoutHandles());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $cache Cache */
        $cache = $objectManager->create(Cache::class);
        $cache->clean();
        $configFile = file_get_contents(__DIR__ . '/_files/page_layouts.xml');
        $fileResolverMock = $this->getMockBuilder(FileResolverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->willReturn([$configFile]);
        $reader = $objectManager->create(
            Reader::class,
            ['fileResolver' => $fileResolverMock]
        );
        $dataStorage = $objectManager->create(Data::class, ['reader' => $reader]);
        $this->_model = $objectManager->create(
            Config::class,
            ['dataStorage' => $dataStorage]
        );
    }
}

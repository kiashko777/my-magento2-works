<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Utility;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    /**
     * @var Layout
     */
    protected $_utility;

    /**
     * @param string|array $inputFiles
     * @param string $expectedFile
     *
     * @dataProvider getLayoutFromFixtureDataProvider
     */
    public function testGetLayoutUpdateFromFixture($inputFiles, $expectedFile)
    {
        $layoutUpdate = $this->_utility->getLayoutUpdateFromFixture($inputFiles);
        $this->_assertLayoutUpdate($expectedFile, $layoutUpdate);
    }

    /**
     * Assert that the actual layout update instance represents the expected layout update file
     *
     * @param string $expectedUpdateFile
     * @param ProcessorInterface $actualUpdate
     */
    protected function _assertLayoutUpdate($expectedUpdateFile, $actualUpdate)
    {
        $this->assertInstanceOf(ProcessorInterface::class, $actualUpdate);

        $layoutUpdateXml = $actualUpdate->getFileLayoutUpdatesXml();
        $this->assertInstanceOf(Element::class, $layoutUpdateXml);
        $this->assertXmlStringEqualsXmlFile($expectedUpdateFile, $layoutUpdateXml->asNiceXml());
    }

    /**
     * @param string|array $inputFiles
     * @param string $expectedFile
     *
     * @dataProvider getLayoutFromFixtureDataProvider
     */
    public function testGetLayoutFromFixture($inputFiles, $expectedFile)
    {
        $layout = $this->_utility->getLayoutFromFixture($inputFiles, $this->_utility->getLayoutDependencies());
        $this->assertInstanceOf(LayoutInterface::class, $layout);
        $this->_assertLayoutUpdate($expectedFile, $layout->getUpdate());
    }

    public function getLayoutFromFixtureDataProvider()
    {
        return [
            'single fixture file' => [
                __DIR__ . '/_files/layout/handle_two.xml',
                __DIR__ . '/_files/layout_merged/single_handle.xml',
            ],
            'multiple fixture files' => [
                glob(__DIR__ . '/_files/layout/*.xml'),
                __DIR__ . '/_files/layout_merged/multiple_handles.xml',
            ]
        ];
    }

    protected function setUp(): void
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(
            [
                Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => [
                    DirectoryList::APP => ['path' => BP . '/dev/tests/integration'],
                ],
            ]
        );
        $this->_utility = new Layout($this);
    }
}

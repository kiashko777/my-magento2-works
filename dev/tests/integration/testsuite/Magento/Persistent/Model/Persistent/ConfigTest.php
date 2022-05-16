<?php
/**
 * \Magento\Persistent\Model\Persistent\Config
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Persistent\Model\Persistent;

use Magento\Catalog\Block\Product\Compare\ListCompare;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Block\Reorder\Sidebar;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $_model;

    /** @var  ObjectManagerInterface */
    protected $_objectManager;

    public function testCollectInstancesToEmulate()
    {
        $this->_model->setConfigFilePath(__DIR__ . '/_files/persistent.xml');
        $result = $this->_model->collectInstancesToEmulate();
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testGetBlockConfigInfo()
    {
        $this->_model->setConfigFilePath(__DIR__ . '/_files/persistent.xml');
        $blocks = $this->_model->getBlockConfigInfo(Sidebar::class);
        $expected = include '_files/expectedBlocksArray.php';
        $this->assertEquals($expected, $blocks);
    }

    public function testGetBlockConfigInfoNotConfigured()
    {
        $this->_model->setConfigFilePath(__DIR__ . '/_files/persistent.xml');
        $blocks = $this->_model->getBlockConfigInfo(ListCompare::class);
        $this->assertEquals([], $blocks);
    }

    protected function setUp(): void
    {
        $directoryList = Bootstrap::getObjectManager()->create(
            DirectoryList::class,
            ['root' => DirectoryList::ROOT]
        );
        $filesystem = Bootstrap::getObjectManager()->create(
            Filesystem::class,
            ['directoryList' => $directoryList]
        );

        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create(
            Config::class,
            ['filesystem' => $filesystem]
        );
    }
}

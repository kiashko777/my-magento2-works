<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Config\Model\Config\Structure\Reader;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SystemConfigFilesTest extends TestCase
{
    public function testConfiguration()
    {
        $objectManager = Bootstrap::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheState StateInterface */
        $cacheState = $objectManager->get(StateInterface::class);
        $cacheState->setEnabled(Config::TYPE_IDENTIFIER, false);

        /** @var Filesystem $filesystem */
        $filesystem = $objectManager->get(Filesystem::class);
        $modulesDir = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        /** @var $moduleDirSearch DirSearch */
        $moduleDirSearch = $objectManager->get(DirSearch::class);
        $fileList = $moduleDirSearch
            ->collectFiles(ComponentRegistrar::MODULE, 'etc/Adminhtml/system.xml');
        $configMock = $this->createPartialMock(
            \Magento\Framework\Module\Dir\Reader::class,
            ['getConfigurationFiles', 'getModuleDir']
        );
        $configMock->expects($this->any())->method('getConfigurationFiles')->willReturn($fileList);
        $configMock->expects(
            $this->any()
        )->method(
            'getModuleDir'
        )->with(
            'etc',
            'Magento_Backend'
        )->willReturn(
            $modulesDir->getAbsolutePath() . '/app/code/Magento/Backend/etc'
        );
        try {
            $objectManager->create(
                Reader::class,
                ['moduleReader' => $configMock, 'runtimeValidation' => true]
            );
        } catch (LocalizedException $exp) {
            $this->fail($exp->getMessage());
        }
    }
}

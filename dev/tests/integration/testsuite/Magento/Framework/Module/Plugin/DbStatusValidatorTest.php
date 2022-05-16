<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Module\Plugin;

use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Module\ResourceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

class DbStatusValidatorTest extends AbstractController
{
    public function testValidationUpToDateDb()
    {
        $this->dispatch('index/index');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testValidationOutdatedDb()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Please upgrade your database');

        $this->markTestSkipped();
        $objectManager = Bootstrap::getObjectManager();

        /** @var ModuleListInterface $moduleList */
        $moduleList = $objectManager->get(ModuleListInterface::class);

        $moduleNameToTest = '';

        // get first module name, we don't care which one it is.
        foreach ($moduleList->getNames() as $moduleName) {
            $moduleNameToTest = $moduleName;
            break;
        }
        $moduleList->getOne($moduleName);

        // Prepend '0.' to DB Version, to cause it to be an older version
        /** @var ResourceInterface $resource */
        $resource = $objectManager->create(ResourceInterface::class);
        $currentDbVersion = $resource->getDbVersion($moduleNameToTest);
        $resource->setDataVersion($moduleNameToTest, '0.' . $currentDbVersion);

        /** @var FrontendInterface $cache */
        $cache = $this->_objectManager->get(Config::class);
        $cache->clean();

        /* This triggers plugin to be executed */
        $this->dispatch('index/index');
    }
}

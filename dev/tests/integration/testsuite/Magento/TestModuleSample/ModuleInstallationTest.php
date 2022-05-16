<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleSample;

use Magento\Framework\Module\ModuleListInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ModuleInstallationTest extends TestCase
{
    public function testSampleModuleInstallation()
    {
        /** @var ModuleListInterface $moduleList */
        $moduleList = Bootstrap::getObjectManager()->get(
            ModuleListInterface::class
        );
        $this->assertTrue(
            $moduleList->has('Magento_TestModuleSample'),
            'Test module [Magento_TestModuleSample] is not installed'
        );
    }
}

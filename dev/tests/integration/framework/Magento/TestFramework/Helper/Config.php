<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Helper with routines to work with Magento config
 */

namespace Magento\TestFramework\Helper;

use Magento\Framework\Module\ModuleListInterface;

class Config
{
    /**
     * Returns enabled modules in the system
     *
     * @return array
     */
    public function getEnabledModules()
    {
        /** @var ModuleListInterface $moduleList */
        $moduleList = Bootstrap::getObjectManager()->get(
            ModuleListInterface::class
        );
        return $moduleList->getNames();
    }
}

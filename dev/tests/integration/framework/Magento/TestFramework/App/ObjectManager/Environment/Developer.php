<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\App\ObjectManager\Environment;

use Magento\TestFramework\ObjectManager\Config;

class Developer extends \Magento\Framework\App\ObjectManager\Environment\Developer
{
    public function getDiConfig()
    {
        if (!$this->config) {
            $this->config = new Config(
                $this->envFactory->getRelations(),
                $this->envFactory->getDefinitions()
            );
        }

        return $this->config;
    }
}

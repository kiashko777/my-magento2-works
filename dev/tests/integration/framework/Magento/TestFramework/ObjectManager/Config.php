<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\ObjectManager;

use Magento\Framework\Interception\ObjectManager\Config\Developer;

class Config extends Developer
{
    /**
     * Clean configuration
     */
    public function clean()
    {
        $this->_preferences = [];
        $this->_virtualTypes = [];
        $this->_arguments = [];
        $this->_nonShared = [];
        $this->_mergedArguments = [];
    }
}

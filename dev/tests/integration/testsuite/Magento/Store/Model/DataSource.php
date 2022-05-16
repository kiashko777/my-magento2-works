<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model;

use Magento\Framework\Data\Collection;

/**
 * Dummy layout argument data source object
 */
class DataSource extends Collection
{
    /**
     * Property which stores all updater calls
     *
     * @var array
     */
    protected $_calls = [];

    /**
     * Return current updater calls
     *
     * @return array
     */
    public function getUpdaterCall()
    {
        return $this->_calls;
    }

    /**
     * Set updater calls
     *
     * @param array $calls
     * @return DataSource
     */
    public function setUpdaterCall(array $calls)
    {
        $this->_calls = $calls;
        return $this;
    }
}

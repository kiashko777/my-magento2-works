<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleMSC\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TestModuleMSC\Api\Data\ItemExtensionInterface;
use Magento\TestModuleMSC\Api\Data\ItemInterface;

/**
 * Class Item
 *
 * @method ItemExtensionInterface getExtensionAttributes()
 */
class Item extends AbstractExtensibleModel implements ItemInterface
{
    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->_data['item_id'];
    }

    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        return $this->setData('item_id', $itemId);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_data['name'];
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleMSC\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TestModuleMSC\Api\Data\CustomAttributeNestedDataObjectExtensionInterface;
use Magento\TestModuleMSC\Api\Data\CustomAttributeNestedDataObjectInterface;

/**
 * Class CustomAttributeNestedDataObject
 *
 * @method CustomAttributeNestedDataObjectExtensionInterface getExtensionAttributes()
 */
class CustomAttributeNestedDataObject extends AbstractExtensibleModel implements
    CustomAttributeNestedDataObjectInterface
{
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

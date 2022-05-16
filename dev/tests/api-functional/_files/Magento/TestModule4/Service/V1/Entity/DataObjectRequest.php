<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule4\Service\V1\Entity;

use Magento\Framework\Api\AbstractExtensibleObject;

class DataObjectRequest extends AbstractExtensibleObject
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_get('name');
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->_get('entity_id');
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }
}

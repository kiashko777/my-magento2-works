<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule4\Service\V1\Entity;

use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class ExtensibleRequest
 *
 * @method ExtensibleRequestExtensionInterface getExtensionAttributes()
 */
class ExtensibleRequest extends AbstractExtensibleModel implements ExtensibleRequestInterface
{
    public function getName()
    {
        return $this->getData("name");
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData("name", $name);
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData("entity_id", $entityId);
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule4\Service\V1\Entity;

use Magento\Framework\Api\AbstractExtensibleObject;

class NestedDataObjectRequest extends AbstractExtensibleObject
{
    /**
     * @return DataObjectRequest
     */
    public function getDetails()
    {
        return $this->_get('details');
    }

    /**
     * @param DataObjectRequest $details
     * @return $this
     */
    public function setDetails(DataObjectRequest $details = null)
    {
        return $this->setData('details', $details);
    }
}

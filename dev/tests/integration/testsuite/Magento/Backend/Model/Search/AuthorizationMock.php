<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Model\Search;

use Magento\Framework\Authorization;

/**
 * @SuppressWarnings("unused")
 */
class AuthorizationMock extends Authorization
{
    /**
     * Check current user permission on resource and privilege
     *
     * @param string $resource
     * @param string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        return true;
    }
}

<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdminNotification\Controller\Adminhtml\Notification;

use Magento\TestFramework\TestCase\AbstractBackendController;

class RemoveTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Magento_AdminNotification::adminnotification_remove';
        $this->uri = 'backend/admin/notification/remove';
        parent::setUp();
    }
}

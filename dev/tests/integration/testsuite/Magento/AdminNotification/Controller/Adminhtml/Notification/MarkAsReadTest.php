<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdminNotification\Controller\Adminhtml\Notification;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Testing markAsRead controller.
 *
 * @magentoAppArea Adminhtml
 */
class MarkAsReadTest extends AbstractBackendController
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->resource = 'Magento_AdminNotification::mark_as_read';
        $this->uri = 'backend/admin/notification/markasread';
        parent::setUp();
    }
}

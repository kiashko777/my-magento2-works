<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdminNotification\Model\ResourceModel\Inbox\Collection;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CriticalTest extends TestCase
{
    /**
     * @var Critical
     */
    protected $_model;

    /**
     * @magentoDataFixture Magento/AdminNotification/_files/notifications.php
     */
    public function testCollectionContainsLastUnreadCriticalItem()
    {
        $items = array_values($this->_model->getItems());
        $this->assertEquals('Unread Critical 3', $items[0]->getTitle());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Critical::class
        );
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Model\ResourceModel\Subscriber;

use Magento\Newsletter\Model\Subscriber;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_collectionModel;

    /**
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testShowCustomerInfo()
    {
        $this->_collectionModel->showCustomerInfo()->load();

        /** @var Subscriber[] $subscribers */
        $subscribers = $this->_collectionModel->getItems();
        $this->assertCount(3, $subscribers);

        while ($subscribers) {
            $subscriber = array_shift($subscribers);
            if ($subscriber->getCustomerId()) {
                $this->assertEquals('John', $subscriber->getFirstname(), $subscriber->getSubscriberEmail());
                $this->assertEquals('Smith', $subscriber->getLastname(), $subscriber->getSubscriberEmail());
            } else {
                $this->assertNull($subscriber->getFirstname(), $subscriber->getSubscriberEmail());
                $this->assertNull($subscriber->getLastname(), $subscriber->getSubscriberEmail());
            }
        }
    }

    protected function setUp(): void
    {
        $this->_collectionModel = Bootstrap::getObjectManager()
            ->create(Collection::class);
    }
}

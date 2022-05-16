<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\User\Controller\Adminhtml\Locks;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\User\Model\User;

/**
 * Testing unlock controller.
 *
 * @magentoAppArea Adminhtml
 */
class MassUnlockTest extends AbstractBackendController
{
    /**
     * Test index action
     *
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/User/_files/locked_users.php
     */
    public function testMassUnlockAction()
    {
        $userIds = [];
        $objectManager = Bootstrap::getObjectManager();
        /** @var $model User */
        $model = $objectManager->create(User::class);
        $userIds[] = $model->loadByUsername('adminUser1')->getId();
        $userIds[] = $model->loadByUsername('adminUser2')->getId();

        $request = $this->getRequest();
        $request->setPostValue(
            'unlock',
            $userIds
        );
        $this->dispatch('backend/admin/locks/massunlock');

        $this->assertSessionMessages(
            $this->containsEqual((string)__('Unlocked %1 user(s).', count($userIds))),
            MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect();
    }
}

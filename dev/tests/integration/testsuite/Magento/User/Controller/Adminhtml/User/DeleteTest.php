<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\User\Controller\Adminhtml\User;

use Magento\Framework\Message\ManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\User\Model\User;

/**
 * Test class for \Magento\User\Controller\Adminhtml\User\Delete
 * @magentoAppArea Adminhtml
 */
class DeleteTest extends AbstractBackendController
{
    /**
     * @covers \Magento\User\Controller\Adminhtml\User\Delete::execute
     */
    public function testDeleteActionWithError()
    {
        $user = Bootstrap::getObjectManager()
            ->create(User::class);
        /** @var ManagerInterface $messageManager */
        $messageManager = Bootstrap::getObjectManager()
            ->get(ManagerInterface::class);
        $user->load(1);
        $this->getRequest()->setPostValue('user_id', $user->getId() . '_suffix_ignored_in_mysql_casting_to_int');

        $this->dispatch('backend/admin/user/delete');
        $message = $messageManager->getMessages()->getLastAddedMessage()->getText();
        $this->assertEquals('You cannot delete your own account.', $message);
    }
}

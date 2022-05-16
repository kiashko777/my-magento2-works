<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\Backend\Block\System\Account\Edit\Form;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\TestFramework\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\User\Model\User;

/**
 * @magentoAppArea Adminhtml
 */
class AccountTest extends AbstractBackendController
{
    /**
     * @dataProvider saveDataProvider
     * @magentoDbIsolation enabled
     */
    public function testSaveAction($password, $passwordConfirmation, $isPasswordChanged)
    {
        $userId = $this->_session->getUser()->getId();
        /** @var $user User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            User::class
        )->load(
            $userId
        );
        $oldPassword = $user->getPassword();

        $request = $this->getRequest();
        $request->setParam(
            'username',
            $user->getUsername()
        )->setParam(
            'email',
            $user->getEmail()
        )->setParam(
            'firstname',
            $user->getFirstname()
        )->setParam(
            'lastname',
            $user->getLastname()
        )->setParam(
            'password',
            $password
        )->setParam(
            'password_confirmation',
            $passwordConfirmation
        )->setParam(
            Form::IDENTITY_VERIFICATION_PASSWORD_FIELD,
            Bootstrap::ADMIN_PASSWORD
        );
        $this->dispatch('backend/admin/system_account/save');

        /** @var $user User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            User::class
        )->load(
            $userId
        );

        if ($isPasswordChanged) {
            $this->assertNotEquals($oldPassword, $user->getPassword());
            $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
            /** @var $encryptor EncryptorInterface */
            $encryptor = $objectManager->get(EncryptorInterface::class);
            $this->assertTrue($encryptor->validateHash($password, $user->getPassword()));
        } else {
            $this->assertEquals($oldPassword, $user->getPassword());
        }
    }

    public function saveDataProvider()
    {
        $password = uniqid('123q');
        return [
            [$password, $password, true],
            [$password, '', false],
            [$password, $password . '123', false],
            ['', '', false],
            ['', $password, false]
        ];
    }
}

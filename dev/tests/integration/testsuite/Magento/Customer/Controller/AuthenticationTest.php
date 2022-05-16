<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Controller;

use Magento\Customer\Controller\Plugin\Account as AccountPlugin;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Set of Tests to verify that Authentication methods work properly
 */
class AuthenticationTest extends AbstractController
{
    /**
     * After changes to `di.xml` and overriding list of allowed actions, unallowed ones should cause redirect.
     */
    public function testExpectRedirectResponseWhenDispatchNotAllowedAction()
    {
        $this->overrideAllowedActions(['notExistingRoute']);

        $this->dispatch('customer/account/create');
        $this->assertRedirect($this->stringContains('customer/account/login'));
    }

    /**
     * Overrides list of `allowedActions` for Authorization Plugin
     *
     * @param string[] $allowedActions
     * @see \Magento\Customer\Controller\Plugin\Account
     */
    private function overrideAllowedActions(array $allowedActions): void
    {
        $allowedActions = array_combine($allowedActions, $allowedActions);
        $pluginFake = $this->_objectManager->create(AccountPlugin::class, ['allowedActions' => $allowedActions]);
        $this->_objectManager->addSharedInstance($pluginFake, AccountPlugin::class);
    }

    /**
     * Allowed actions should be rendered normally
     */
    public function testExpectPageResponseWhenAllowedAction()
    {
        $this->overrideAllowedActions(['create']);

        $this->dispatch('customer/account/create');
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
    }

    /**
     * Make sure that customized AccountPlugin was reverted.
     */
    protected function tearDown(): void
    {
        $this->resetAllowedActions();
        parent::tearDown();
    }

    /**
     * Removes all the customizations applied to `allowedActions`
     * @see \Magento\Customer\Controller\Plugin\Account
     */
    private function resetAllowedActions()
    {
        $this->_objectManager->removeSharedInstance(AccountPlugin::class);
    }
}

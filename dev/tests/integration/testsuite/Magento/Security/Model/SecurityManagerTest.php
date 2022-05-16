<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Security\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Security\Model\ResourceModel\PasswordResetRequestEvent\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SecurityManagerTest extends TestCase
{
    /**
     * @var  SecurityManager
     */
    protected $securityManager;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var PasswordResetRequestEvent
     */
    protected $passwordResetRequestEvent;

    /**
     * Test for performSecurityCheck() method
     *
     * @magentoConfigFixture current_store customer/password/limit_password_reset_requests_method 0
     * @magentoDbIsolation enabled
     */
    public function testPerformSecurityCheck()
    {
        $collection = $this->getPasswordResetRequestEventCollection();
        $sizeBefore = $collection->getSize();

        $requestType = PasswordResetRequestEvent::CUSTOMER_PASSWORD_RESET_REQUEST;
        $longIp = 127001;
        $accountReference = 'customer@example.com';
        $this->assertInstanceOf(
            SecurityManager::class,
            $this->securityManager->performSecurityCheck(
                $requestType,
                $accountReference,
                $longIp
            )
        );

        $collection = $this->getPasswordResetRequestEventCollection();
        $sizeAfter = $collection->getSize();
        $this->assertEquals(1, $sizeAfter - $sizeBefore);
    }

    /**
     * Get PasswordResetRequestEvent collection
     *
     * @return Collection
     */
    protected function getPasswordResetRequestEventCollection()
    {
        $collection = $this->passwordResetRequestEvent->getResourceCollection();
        $collection->load();

        return $collection;
    }

    /**
     * Test for performSecurityCheck() method when number of password reset events is exceeded
     *
     * @magentoConfigFixture current_store customer/password/limit_password_reset_requests_method 1
     * @magentoConfigFixture current_store customer/password/max_number_password_reset_requests 1
     * @magentoConfigFixture current_store customer/password/min_time_between_password_reset_requests 0
     * @magentoConfigFixture current_store contact/email/recipient_email hi@example.com
     * @magentoDbIsolation enabled
     */
    public function testPerformSecurityCheckLimitNumber()
    {
        $this->expectException(SecurityViolationException::class);

        $attempts = 2;
        $requestType = PasswordResetRequestEvent::CUSTOMER_PASSWORD_RESET_REQUEST;
        $longIp = 127001;
        $accountReference = 'customer@example.com';

        try {
            for ($i = 0; $i < $attempts; $i++) {
                $this->securityManager->performSecurityCheck($requestType, $accountReference, $longIp);
            }
        } catch (SecurityViolationException $e) {
            $this->assertEquals(1, $i);
            throw new SecurityViolationException(
                __($e->getMessage())
            );
        }

        $this->expectExceptionMessage(
            'We received too many requests for password resets. '
            . 'Please wait and try again later or contact hi@example.com.'
        );
    }

    /**
     * Test for performSecurityCheck() method when time between password reset events is exceeded
     *
     * @magentoConfigFixture current_store customer/password/limit_password_reset_requests_method 1
     * @magentoConfigFixture current_store customer/password/max_number_password_reset_requests 0
     * @magentoConfigFixture current_store customer/password/min_time_between_password_reset_requests 1
     * @magentoConfigFixture current_store contact/email/recipient_email hi@example.com
     * @magentoDbIsolation enabled
     */
    public function testPerformSecurityCheckLimitTime()
    {
        $this->expectException(SecurityViolationException::class);

        $attempts = 2;
        $requestType = PasswordResetRequestEvent::CUSTOMER_PASSWORD_RESET_REQUEST;
        $longIp = 127001;
        $accountReference = 'customer@example.com';

        try {
            for ($i = 0; $i < $attempts; $i++) {
                $this->securityManager->performSecurityCheck($requestType, $accountReference, $longIp);
            }
        } catch (SecurityViolationException $e) {
            $this->assertEquals(1, $i);
            throw new SecurityViolationException(
                __($e->getMessage())
            );
        }

        $this->fail('Something went wrong. Please check method execution logic.');

        $this->expectExceptionMessage(
            'We received too many requests for password resets. '
            . 'Please wait and try again later or contact hi@example.com.'
        );
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->accountManagement = $this->objectManager->create(
            AccountManagementInterface::class
        );
        $this->securityManager = $this->objectManager->create(SecurityManager::class);
        $this->passwordResetRequestEvent = $this->objectManager
            ->get(PasswordResetRequestEvent::class);
    }

    /**
     * Tear down
     */
    protected function tearDown(): void
    {
        $this->objectManager = null;
        $this->accountManagement = null;
        $this->securityManager = null;
        parent::tearDown();
    }
}

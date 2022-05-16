<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\User\Helper;

use Magento\Backend\App\ConfigInterface;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class DataTest extends TestCase
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * Test generate unique token for reset password confirmation link
     *
     * @covers \Magento\User\Helper\Data::generateResetPasswordLinkToken
     */
    public function testGenerateResetPasswordLinkToken()
    {
        $actual = $this->_helper->generateResetPasswordLinkToken();
        $this->assertGreaterThan(15, strlen($actual));
    }

    /**
     * Test retrieve customer reset password link expiration period in days
     *
     */
    public function testGetResetPasswordLinkExpirationPeriod()
    {
        /** @var $configModel ConfigInterface */
        $configModel = Bootstrap::getObjectManager()->get(
            MutableScopeConfigInterface::class
        );
        $this->assertEquals(
            2,
            (int)$configModel->getValue(
                Data::XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
            )
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}

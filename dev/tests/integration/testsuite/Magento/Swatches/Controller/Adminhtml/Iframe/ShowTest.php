<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Swatches\Controller\Adminhtml\Iframe;

use Magento\Framework\Acl;
use Magento\Framework\Acl\Builder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class ShowTest extends AbstractBackendController
{
    /**
     * Check Swatch Acl Access
     */
    public function testAclAccess()
    {
        /** @var $acl Acl */
        $acl = Bootstrap::getObjectManager()
            ->get(Builder::class)
            ->getAcl();

        $acl->allow(null, Show::ADMIN_RESOURCE);

        $this->dispatch('backend/swatches/iframe/show/');

        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
        $this->assertStringNotContainsString(
            'Sorry, you need permissions to view this content.',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Check Swatch Acl Access Denied
     */
    public function testAclAccessDenied()
    {
        /** @var $acl Acl */
        $acl = Bootstrap::getObjectManager()
            ->get(Builder::class)
            ->getAcl();

        $acl->deny(null, Show::ADMIN_RESOURCE);

        $this->dispatch('backend/swatches/iframe/show/');

        $this->assertEquals(403, $this->getResponse()->getHttpResponseCode());
        $this->assertStringContainsString(
            'Sorry, you need permissions to view this content.',
            $this->getResponse()->getBody()
        );
    }
}

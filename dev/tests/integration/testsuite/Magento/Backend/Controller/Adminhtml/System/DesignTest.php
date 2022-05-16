<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class DesignTest extends AbstractBackendController
{
    /**
     * @covers \Magento\Backend\App\Action::_addLeft
     */
    public function testEditAction()
    {
        $this->dispatch('backend/admin/system_design/edit');
        $this->assertStringMatchesFormat('%A<a%Aid="design_tabs_general"%A', $this->getResponse()->getBody());
    }
}

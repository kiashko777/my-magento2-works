<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Controller\Adminhtml;

use Magento\ImportExport\Helper\Data;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class ImportTest extends AbstractBackendController
{
    public function testGetFilterAction()
    {
        $this->dispatch('backend/admin/import/index');
        $body = $this->getResponse()->getBody();
        $this->assertStringContainsString(
            (string)Bootstrap::getObjectManager()->get(
                Data::class
            )->getMaxUploadSizeMessage(),
            $body
        );
    }
}
